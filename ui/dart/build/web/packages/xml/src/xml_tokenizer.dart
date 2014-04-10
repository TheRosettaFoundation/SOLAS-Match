part of xml_utils_prujohn;

/**
* Returns tokenized parts of Xml document.
*/
class XmlTokenizer {
  static const int TAB = 9;
  static const int NEW_LINE = 10;
  static const int CARRIAGE_RETURN = 13;
  static const int SPACE = 32;
  static const int QUOTE = 34;
  static const int SQUOTE = 39;
  static const int SLASH = 47;
  static const int COLON = 58;
  static const int LT = 60; //<
  static const int GT = 62; //>
  static const int EQ = 61; //=
  static const int Q = 63;  //?
  static const int B = 33;  //!
  static const int DASH = 45; //-
  static const int RBRACK = 93; //]

  static const String ERR_UNEXPECTED_END = 'Tokenizer unexpectedly reached end of document';

  static const List _reserved = const [LT, GT, B, COLON, SLASH, QUOTE,
                                      SQUOTE, EQ];

  static const List _whiteSpace = const[SPACE, TAB, NEW_LINE, CARRIAGE_RETURN];

  static const _specialTags = const ['<!--', '<![CDATA[', '<?', '</', '<!DOCTYPE'];

  final ListQueue<XmlToken> _buffer = new ListQueue<XmlToken>();
  final List<XmlToken> _tokenized = new List<XmlToken>();
  final String _xml;
  int _length;
  int _i = 0;
  int _index = -1;
  bool _isInTag = false;

  int get lastTokenIndex => _index;

  XmlTokenizer(this._xml)
  {
    _length = _xml.length;

    var t = _next();
    while(t != null){
      _tokenized.add(t);
      t = _next();
    }
  }

  /**
   * Returns the next token, or null if no tokens are available.
   */
  XmlToken next(){
    if (_tokenized.isEmpty){
      return null;
    }

    _index++;
    return _tokenized.removeAt(0);
  }


  /**
   * Returns -1 if the token is not found, otherwise returns the index of
   * the first instance of the token in the token sequence.
   */
  int indexOfToken(XmlToken token, {start: 0}){
    if (_tokenized.isEmpty) return -1;

    if (start < 0 || start > _tokenized.length - 1){
      throw new RangeError(0);
    }

    int i = start;
    for(final t in _tokenized.skip(start).take(_tokenized.length - start)){
      if (t.kind == token.kind){
        if (t.kind == XmlToken.STRING){
          if (t._str == token._str){
            return i;
          }
        }else if (t.kind == XmlToken.QUOTE){
          if (t.quoteKind == token.quoteKind){
            return i;
          }
        }else{
          return i;
        }
      }
      i++;
    }

    return -1;

  }

  /**
   * Performs a non-destructive look-ahead in the token list and tries to match
   * the given [sequence] of tokens.
   *
   * Search will continue until the end of the token list, or until optional
   * [until] sequence of tokens is found.
   *
   * Search begins at [index] = 0 unless otherwise specified.
   */
  bool lookAheadMatch(List<XmlToken> sequence,
            {List<XmlToken> until: null, int index : 0})
  {
    if (index < 0 || index > _tokenized.length - 1) return false;
    if (index + sequence.length > _tokenized.length) return false;

    var resultUntil = until == null
        ? _tokenized.length
        : _sequenceMatch(until, index, _tokenized.length);

    if (resultUntil == -1) resultUntil = _tokenized.length;

    var result = _sequenceMatch(sequence, index, resultUntil);

    if (result == -1)
    {
      return false;
    }
    if (resultUntil < result)
    {
      return false;
    }

    return true;
  }

  /**
   * -1 if not found, otherwise returns start index of matching sequence
   * in _tokenized. No boundary checks.
   */
  int _sequenceMatch(Iterable<XmlToken> sequence, int index, int until){
    final result = indexOfToken(sequence.elementAt(0), start:index);

    if (result == -1) return -1;

    if (sequence.length == 1) return result;

    return _sequenceMatch(
        sequence.skip(1).take(sequence.length - 1),
        result,
        until);

  }

  XmlToken _next() {
    void addToQueue(XmlToken token){
      token._location = _i;
      _buffer.addLast(token);
    }

    XmlToken getNextToken() {
//      if (!_tq.isEmpty()){
//        print('token: ${_tq.first()}, ${_tq.first()._str}');
//      }
      return _buffer.isEmpty ? null : _buffer.removeFirst();
    }


    // Returns the first char in the list that appears ahead.
    int peekUntil(List chars){
      int z = _i;

      while (chars.indexOf(_xml.codeUnitAt(z)) == -1){
        z++;
        if (z >= _length) break;
      }

      if (z >= _length) return -1;

      return _xml.codeUnitAt(z);
    }

    // Returns the index of the last char of a given word, if found from
    // the current index onward; otherwise returns -1;
    int matchWord(String word){
      int z = _i;

      for(int ii = 0; ii < word.length; ii++){
        if(_xml.codeUnitAt(z) != word.codeUnitAt(ii)) return -1;
        z++;
      }

      return z - 1;
    }

    int nextNonWhitespace(int from){

      while(isWhitespace(_xml.codeUnitAt(from))){
        from++;
      }
      return from;
    }

    int nextWhitespace(int from){
      while(!isWhitespace(_xml.codeUnitAt(from))){
        from++;
      }
      return from;
    }

    // Peel off and return a token if there are any in the queue.
    if (!_buffer.isEmpty) return getNextToken();

    while(_i < _length && isWhitespace(_xml.codeUnitAt(_i)))
      {
        _i++;
      }

    if (_i == _length) return null;
  //print('char: $_i code: ${_xml.charCodeAt(_i)} ' + _xml.substring(_i, _i+1));
    final int char = _xml.codeUnitAt(_i);

    switch(char){
      case B:
        _i++;
        addToQueue(new XmlToken(XmlToken.BANG));
        break;
      case COLON:
        _i++;
        addToQueue(new XmlToken(XmlToken.COLON));
        break;
      case SLASH:
        _i++;
        addToQueue(new XmlToken(XmlToken.SLASH));
        break;
      case LT:
        var found = '';
        var endIndex = -1;

        for(final tag in _specialTags){
          var m = matchWord(tag);
          if (m != -1){
            found = tag;
            endIndex = m;
            break;
          }
        }

        if (found == _specialTags[0]) {
          addToQueue(new XmlToken(XmlToken.START_COMMENT));
          _i = endIndex + 1;

          var endComment = _xml.indexOf('-->', _i);
          var nestedTest = _xml.indexOf('<!--', _i);

          if (endComment == -1){
            throw const XmlException('End comment tag not found.');
          }

          if (nestedTest != -1 && nestedTest < endComment){
            throw const XmlException('Nested comments not allowed.');
          }

          addToQueue(new XmlToken.string(_xml.substring(_i, endComment)));
          addToQueue(new XmlToken(XmlToken.END_COMMENT));
          _i = endComment + 3;
        } else if (found == _specialTags[1]) {
          addToQueue(new XmlToken(XmlToken.START_CDATA));
          _i = endIndex + 1;

          var endCDATA = _xml.indexOf(']]>', _i);
          var nestedTest = _xml.indexOf('<![CDATA[', _i);

          if (endCDATA == -1){
            throw const XmlException('End CDATA tag not found.');
          }

          if (nestedTest != -1 && nestedTest < endCDATA){
            throw const XmlException('Nested CDATA not allowed.');
          }

          addToQueue(new XmlToken.string(_xml.substring(_i, endCDATA).trim()));
          addToQueue(new XmlToken(XmlToken.END_CDATA));
          _i = endCDATA + 3;
        } else if (found == _specialTags[2]) {
          addToQueue(new XmlToken(XmlToken.START_PI));
          _i = endIndex + 1;

          var endPI= _xml.indexOf('?>', _i);
          var nestedTest = _xml.indexOf('<?', _i);

          if (endPI == -1){
            throw const XmlException('End PI tag not found.');
          }

          if (nestedTest != -1 && nestedTest < endPI){
            throw const XmlException('Nested PI not allowed.');
          }

          addToQueue(new XmlToken.string(_xml.substring(_i, endPI).trim()));
          addToQueue(new XmlToken(XmlToken.END_PI));
          _i = endPI+ 2;
        } else if (found == _specialTags[3]) {
          addToQueue(new XmlToken(XmlToken.LT));
          addToQueue(new XmlToken(XmlToken.SLASH));
          _i = endIndex + 1;
        } else if (found == _specialTags[4]) {
          // DOCTYPE
        } else {
          //standard start tag
          _i++;
          addToQueue(new XmlToken(XmlToken.LT));
          _i = nextNonWhitespace(_i);
          int c = peekUntil([SPACE, COLON, GT]);
          if (c == SPACE){
            var _ii = _i;
            _i = nextWhitespace(_ii);
            addToQueue(new XmlToken.string(_xml.substring(_ii, _i)));
            _i = nextNonWhitespace(_i);
          }else if (c == COLON){
            var _ii = _i;
            _i = _xml.indexOf(':', _ii) + 1;
            addToQueue(new XmlToken.string(_xml.substring(_ii, _i - 1)));
            addToQueue(new XmlToken(XmlToken.COLON));
            _ii = _xml.indexOf(new String.fromCharCode(peekUntil([SPACE, SLASH, GT])), _i);
            addToQueue(new XmlToken.string(_xml.substring(_i, _ii)));
            _i = nextNonWhitespace(_ii);
          }else if (c == -1){
            throw new XmlException(ERR_UNEXPECTED_END);
          }
          _isInTag = true;
        }
        break;
      case GT:
        _i++;
        addToQueue(new XmlToken(XmlToken.GT));
        _isInTag = false;
        break;
      case EQ:
        _i++;
        addToQueue(new XmlToken(XmlToken.EQ));
        break;
      case QUOTE:
        _i++;
        addToQueue(new XmlToken.quote(QUOTE));
        if (_isInTag) {
          if (peekUntil([QUOTE]) == QUOTE) {
            var _ii = _i;
            _i = _xml.indexOf('"', _ii);
            addToQueue(new XmlToken.string(_xml.substring(_ii, _i)));

            _i++;
            addToQueue(new XmlToken.quote(QUOTE));
          } else {
            throw new XmlException(ERR_UNEXPECTED_END);
          }
        }
        break;
      case SQUOTE:
        _i++;
        addToQueue(new XmlToken.quote(SQUOTE));
        if (_isInTag) {
          if (peekUntil([SQUOTE]) == SQUOTE) {
            var _ii = _i;
            _i = _xml.indexOf("'", _ii);
            addToQueue(new XmlToken.string(_xml.substring(_ii, _i)));

            _i++;
            addToQueue(new XmlToken.quote(SQUOTE));
          } else {
            throw new XmlException(ERR_UNEXPECTED_END);
          }
        }
        break;
      default:
        var m = matchWord('xmlns:');
        if (m != -1){
          _i = m + 1;
          addToQueue(new XmlToken(XmlToken.NAMESPACE));
        }else{
          final s = new StringBuffer();
          while(_i < _length && !isReserved(_xml.codeUnitAt(_i))){
            s.write(_xml.substring(_i, _i + 1));
            _i++;
          }
          if (_isInTag) {
            addToQueue(new XmlToken.string(s.toString().trim()));
          } else {
            addToQueue(new XmlToken.string(s.toString()));
          }
        }
        break;
    }
    return getNextToken();
  }

  String toString() => '$_tokenized ::: Length: ${_tokenized.length}';

  /**
  * Returns true if the charCode is one of the special reserved
  * charCodes
  */
  static bool isReserved(int c) => _reserved.indexOf(c) > -1;

  /**
  * Returns true if the charCode is considered to be whitespace.
  */
  static bool isWhitespace(int c) => _whiteSpace.indexOf(c) >= 0;

}

class XmlToken {
  static const int LT = 1;
  static const int GT = 2;
  static const int QUESTION = 3;
  static const int STRING = 4;
  static const int BANG = 5;
  static const int COLON = 6;
  static const int SLASH = 7;
  static const int EQ = 8;
  static const int QUOTE = 9;
  static const int IGNORE = 10;
  static const int DASH = 11;
  static const int START_COMMENT = 12;
  static const int END_COMMENT = 13;
  static const int START_CDATA = 14;
  static const int END_CDATA = 15;
  static const int START_PI = 16;
  static const int END_PI = 17;
  static const int NAMESPACE = 18;

  final int kind;
  final int quoteKind;
  final String _str;
  int _location;

  XmlToken._internal(this.kind, this._str, this.quoteKind);

  factory XmlToken.string(String s) {
    return new XmlToken._internal(STRING, s, -1);
  }

  factory XmlToken.quote(int quoteKind){
    return new XmlToken._internal(QUOTE, '', quoteKind);
  }

  factory XmlToken(int kind) {
    return new XmlToken._internal(kind, '', -1);
  }

  String toString() {
    switch(kind){
      case START_PI:
        return "(<?)";
      case END_PI:
        return "(?>)";
      case DASH:
        return "(-)";
      case LT:
        return "(<)";
      case GT:
        return "(>)";
      case QUESTION:
        return "(?)";
      case STRING:
        return 'STRING($_str)';
      case BANG:
        return "(!)";
      case COLON:
        return "(:)";
      case SLASH:
        return "(/)";
      case EQ:
        return "(=)";
      case QUOTE:
        return '(")';
      case START_COMMENT:
        return '(<!--)';
      case END_COMMENT:
        return '(-->)';
      case START_CDATA:
        return ('(<![CDATA[)');
      case END_CDATA:
        return ('(]]>)');
      case NAMESPACE:
        return ('xmlns');
      case IGNORE:
        return 'INVALID()';
    }
  }

  String toStringLiteral() {
    switch(kind){
      case NAMESPACE:
        return "xmlns";
      case GT:
        return ">";
      case LT:
        return "<";
      case QUESTION:
        return "?";
      case STRING:
        return _str;
      case BANG:
        return "!";
      case COLON:
        return ":";
      case SLASH:
        return "/";
      case EQ:
        return "=";
      case QUOTE:
        return quoteKind == XmlTokenizer.QUOTE ? '"' : "'";
      case IGNORE:
        return 'INVALID()';
      default:
        throw new XmlException('String literal unavailable for $this');
    }
  }
}