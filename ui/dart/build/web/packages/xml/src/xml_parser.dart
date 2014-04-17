part of xml_utils_prujohn;

/**
* ## XML Parser ##
*
* ### When _withQuirks == true: ###
* * Allows optional attribute quotes for single string values
*/
class XmlParser {
  final String _xml;
  final ListQueue<XmlElement> _scopes;
  final bool _withQuirks;
  XmlElement _root;

  static XmlElement _parse(String xml, [withQuirks = false])  {
    if (xml.isEmpty){
      throw const XmlException('Nothing to parse.');
    }
    XmlParser p = new XmlParser._internal(xml, withQuirks);

    final XmlTokenizer t = new XmlTokenizer(p._xml);

    p._parseElement(t);

    return p._root;
  }

  XmlParser._internal(this._xml, this._withQuirks) :
    _scopes = new ListQueue<XmlElement>();

  void _parseElement(XmlTokenizer t) {

    XmlToken tok = t.next();

    while(tok != null){

      switch(tok.kind){
        case XmlToken.START_COMMENT:
          _parseComment(t);
          break;
        case XmlToken.START_CDATA:
          _parseCDATA(t);
          break;
        case XmlToken.START_PI:
          _parsePI(t);
          break;
        case XmlToken.LT:
          _parseTag(t);
          break;
        case XmlToken.STRING:
          if (_scopes.isEmpty){
            throw const XmlException('Text not allowed in root level.'
              ' Use comments instead.');
          }else{
            _parseTextNode(t, tok._str);
            _parseTag(t);
          }
          break;
      }
      tok = t.next();
    }

    if (!_scopes.isEmpty){
      throw const XmlException('Unexpected end of file.  Not all tags were'
        ' closed.');
    }
  }

  _parsePI(XmlTokenizer t){

    XmlToken next = t.next();

    _assertKind(next, XmlToken.STRING);
    var data = next._str;

    next = t.next();
    _assertKind(next, XmlToken.END_PI);

    if (_scopes.isEmpty) {
        if (_root == null && data != null && data.startsWith("xml ")) {
          // Ignore XML declaration at the beginning, i.e <?xml … ?>
          return;
        } else {
            throw const XmlException('PI nodes are not supported in the top'
            ' level.');
        }
    }

    _peek().addChild(new XmlProcessingInstruction(data));
  }

  _parseCDATA(XmlTokenizer t){
    if (_scopes.isEmpty){
      throw const XmlException('CDATA nodes are not supported in the top'
        ' level.');
    }

    XmlToken next = t.next();

    _assertKind(next, XmlToken.STRING);
    var data = next._str;

    next = t.next();
    _assertKind(next, XmlToken.END_CDATA);

    _peek().addChild(new XmlCDATA(data));
  }

  //TODO create and XMLComment object instead of just ignoring?
  _parseComment(XmlTokenizer t){
    XmlToken next = t.next();

    _assertKind(next, XmlToken.STRING);

    next = t.next();
    _assertKind(next, XmlToken.END_COMMENT);
  }

  _parseTag(XmlTokenizer t){
    XmlToken next = t.next();

    if (next == null) return;

    if (next.kind == XmlToken.SLASH){
      // this is a close tag

      next = t.next();
      _assertKind(next, XmlToken.STRING);

      var name = next._str;

      next = t.next();

      if (next.kind == XmlToken.COLON){
        //process as namespace
        next = t.next();

        _assertKind(next, XmlToken.STRING, 'Namespace prefix must pair with'
        ' a tag name: (<myNamespace:tagName ...)');

        name = '${name}:${next._str}';
        next = t.next();
      }


      if (_peek().name != name){
        throw new XmlException.withDebug(
        'Expected closing tag "${_peek().name}"'
        ' but found "${name}" instead.', _xml, next._location);
      }

      _assertKind(next, XmlToken.GT);

      _pop();

      return;
    }

    //otherwise this is an open tag

    _assertKind(next, XmlToken.STRING);

    //TODO check tag name for invalid chars

    var name = next._str;

    next = t.next();

    if (next.kind == XmlToken.COLON){
      //process as namespace
      next = t.next();

      _assertKind(next, XmlToken.STRING, 'Namespace prefix must pair with'
      ' a tag name: (<myNamespace:tagName ...)');

      name = '${name}:${next._str}';
    }

    XmlElement newElement = new XmlElement(name);

    if (_root == null){
      //set to root and push
      _root = newElement;
      _push(_root);
    } else{
      //add child to current scope
      _peek().addChild(newElement);
      _push(newElement);
    }
    
    var peekTag = _peek();

    var ns='';
    if (peekTag.name.contains(':')){
      ns = peekTag.name.split(':')[0];
      next = t.next();
    }

    var done = false;
    while(next != null){

      switch(next.kind){
        case XmlToken.NAMESPACE:
          _parseNamespace(t);
          break;
        case XmlToken.STRING:
          _parseAttribute(t, next._str);
          break;
        case XmlToken.GT:
          _parseElement(t);
          done = true;
          break;
        case XmlToken.SLASH:
          next = t.next();
          _assertKind(next, XmlToken.GT);
          _pop();
          done = true;
          break;
        default:
          throw new XmlException.withDebug(
            'Invalid xml ${next} found at this location.',
            _xml,
            next._location);
      }
      
      if (done)
      {
        break;
      }

      next = t.next();

      if (next == null){
        throw new Exception('Unexpected end of file.');
      }
      
    }
    
    if ((ns.isNotEmpty) && (!peekTag.isNamespaceInScope(ns))){
      throw new XmlException.withDebug('Namespace "${ns}" is'
      ' not declared in scope.', _xml, next._location);      
    }
    
  }

  void _parseTextNode(XmlTokenizer t, String text){

    //in text node all tokens until < are joined to a single string
    StringBuffer s = new StringBuffer();

    writeStringNode(){
      var string = s.toString();
      if (!string.isEmpty) {
        _peek().addChild(new XmlText(s.toString()));
      }
    }

    s.write(text);

    XmlToken next = t.next();

    while(next != null && next.kind != XmlToken.LT){
      switch(next.kind){
        case XmlToken.START_COMMENT:
          writeStringNode();
          _parseComment(t);
          s = new StringBuffer();
          break;
        case XmlToken.START_CDATA:
          writeStringNode();
          _parseCDATA(t);
          s = new StringBuffer();
          break;
        case XmlToken.START_PI:
          writeStringNode();
          _parsePI(t);
          s = new StringBuffer();
          break;
        default:
          s.write(next.toStringLiteral());
          break;
      }

      next = t.next();

      if (next == null){
        throw const XmlException('Unexpected end of file.');
      }
    }

    writeStringNode();
  }

  void _parseNamespace(XmlTokenizer t){
    XmlElement el = _peek();

    void setNamespace(String name, String uri){
      el.namespaces[name] = uri;
    }

    XmlToken next = t.next();
    _assertKind(next, XmlToken.STRING, "Must declare namespace name.");
    var name = next._str;

    next = t.next();
    _assertKind(next, XmlToken.EQ, "Must have an = after a"
      " namespace name.");

    next = t.next();

    void quotesRequired(){
      //require quotes

      _assertKind(next, XmlToken.QUOTE, "Quotes are required around"
        " attribute values.");

      StringBuffer s = new StringBuffer();

      int qkind = next.quoteKind;

      do {
        next = t.next();

        if (next == null){
          throw const XmlException('Unexpected end of file.');
        }

        if (next.kind != XmlToken.QUOTE){
          s.write(next.toStringLiteral());
        }else{
          if (next.quoteKind != qkind){
            s.write(next.toStringLiteral());
          }else{
            qkind = -1;
          }
        }

      } while (qkind != -1);


      setNamespace(name, s.toString());
    }


    if (_withQuirks){
      if (next.kind == XmlToken.STRING){
        setNamespace(name, next._str);
      }else if (next.kind == XmlToken.QUOTE){
        quotesRequired();
      }
    }else{
      quotesRequired();
    }
  }

  void _parseAttribute(XmlTokenizer t, String attributeName){
    XmlElement el = _peek();

    void setAttribute(String name, String value){
      if (name == 'xmlns'){
        // default namespace
        el.namespaces[''] = value;
      }else{
        //TODO validate well-formed attribute names
        el.attributes[name] = value;
      }
    }

    XmlToken next = t.next();

    if (next.kind == XmlToken.COLON){
      //process as namespace
      next = t.next();

      _assertKind(next, XmlToken.STRING, 'Namespace prefix must pair with'
      ' an attribute name: (myNamespace:myattribute="...")');

      var result = t.lookAheadMatch(
       [
       new XmlToken(XmlToken.NAMESPACE),
       new XmlToken.string(attributeName)
       ],
       until:
       [
        new XmlToken(XmlToken.GT)
       ],
       index: t.lastTokenIndex);

      if (!el.isNamespaceInScope(attributeName) && result == false){
        print('$result $attributeName');
        throw new XmlException.withDebug('xxNamespace "$attributeName" is'
          ' not declared in scope.', _xml, next._location);
      }

      attributeName = '${attributeName}:${next._str}';
      next = t.next();
    }

    _assertKind(next, XmlToken.EQ, "Must have an = after an"
      " attribute name.");

    next = t.next();

    void quotesRequired(){
      //require quotes

      _assertKind(next, XmlToken.QUOTE, "Quotes are required around"
        " attribute values.");

      StringBuffer s = new StringBuffer();

      int qkind = next.quoteKind;

      do {
        next = t.next();

        if (next == null){
          throw const XmlException('Unexpected end of file.');
        }

        if (next.kind != XmlToken.QUOTE){
          s.write(next.toStringLiteral());
        }else{
          if (next.quoteKind != qkind){
            s.write(next.toStringLiteral());
          }else{
            qkind = -1;
          }
        }

      } while (qkind != -1);


      setAttribute(attributeName, s.toString());
    }


    if (_withQuirks){
      if (next.kind == XmlToken.STRING){
        setAttribute(attributeName, next._str);
      }else if (next.kind == XmlToken.QUOTE){
        quotesRequired();
      }
    }else{
      quotesRequired();
    }
  }


  void _push(XmlElement element){
  //  print('pushing element ${element.tagName}');
    _scopes.addFirst(element);
  }
  XmlElement _pop(){
  //  print('popping element ${_peek().tagName}');
    _scopes.removeFirst();
  }
  XmlElement _peek() => _scopes.first;

  void _assertKind(XmlToken tok, int matchID, [String info = null]){
    XmlToken match = new XmlToken(matchID);

    var msg = 'Expected ${match}, but found ${tok}. ${info == null ? "" :
      "\r$info"}';

    if (tok != null && tok.kind != match.kind) {
      throw new XmlException.withDebug(msg, _xml, tok._location);
    }
  }
}
