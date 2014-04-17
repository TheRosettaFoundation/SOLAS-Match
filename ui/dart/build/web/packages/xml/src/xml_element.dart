part of xml_utils_prujohn;

/**
* Represents an element node of XML.
*/
class XmlElement extends XmlNode {
  final String name;

  /**
  * Gets a collection of children under this [XmlElement].
  */
  final XmlCollection<XmlNode> children =
      new XmlCollection<XmlNode>._internal();

  /**
  * Gets a map of name/value attributue pairs associated with
  * this [XmlElement].
  */
  final Map<String, String> attributes = new Map<String, String>();

  /**
  * Gets a map of name/uri namespace pairs associated with
  * this [XmlElement].
  */
  final Map<String, String> namespaces = new Map<String, String>();

  //final String namespace; //future

  XmlElement(this.name, {Iterable<XmlNode> elements: const []}) :
    super(XmlNodeType.Element) {
    addChildren(elements);
  }

  /**
  * Gets a [String] of any text within this [XmlElement].
  */
  String get text {
    var tNodes = children.where((el) => el is XmlText);
    if (tNodes.isEmpty) return '';

    var s = new StringBuffer();
    tNodes.forEach((XmlText n) => s.write(n.text));
    return s.toString();
  }

  /**
  * Gets a collection of siblings related to this [XmlElement].
  */
  Iterable<XmlNode> get siblings => parent.children;

  /**
  * Gets a collection of [XmlNamespace]s that are in scope to this
  * [XmlElement].
  */
  Iterable<XmlNamespace> get namespacesInScope {
    List<XmlNamespace> l = [];

    namespaces.forEach((nname, uri){
      l.add(new XmlNamespace(nname, uri));
    });

    if (parent != null && parent is XmlElement){
      l.addAll(parent.namespacesInScope);
    }

    return l;
  }

  /**
  * Returns true if the given [namespace] name is found in the current
  * scope of namespaces.
  */
  bool isNamespaceInScope(String namespace) =>
      namespacesInScope.any((ns) => ns.name == namespace);

  /**
  * Gets the previous sibling to the this [XmlElement], or null if none exists.
  */
  XmlNode get previousSibling {
    var i = parent.children.indexOf(this);

    if (i <= 0) return null;

    return parent.children[i - 1];
  }

  /**
  * Gets the next sibling adjacent to this [XmlElement], or null if none
  * exists.
  */
  XmlNode get nextSibling {
    if (parent.children.last == this) return null;

    var i = parent.children.indexOf(this);

    return parent.children[i + 1];
  }

  /**
  * Gets a boolean indicating of this [XmlElement] has any child elements.
  */
  bool get hasChildren => !children.isEmpty;

  /**
  * Adds a child [XmlNode] to this [XmlElement].
  */
  void addChild(XmlNode element){
    //shunt any XmlAttributes into the map
    if (element is XmlAttribute){
      attributes[element.name] = element.value;
      return;
    }

    //shunt any XmlNamespaces into the map
    if (element is XmlNamespace){
      namespaces[element.name] = element.uri;
      return;
    }

    element.parent = this;
    children.add(element);
  }

  /**
  * Adds a collection of [XmlNode]s to this [XmlElement].
  */
  void addChildren(Iterable<XmlNode> elements){
    elements.forEach(addChild);
  }

  /**
  * Returns the first node in the tree that matches the given [queryOn]
  * parameter.
  *
  * ## Usage ##
  * * query('tagName') // returns first occurance matching tag name.
  * * query(XmlNodeType.CDATA) // returns first occurance of element matching
  * the given node type (CDATA node in this example).
  * * query({'attributeName':'attributeValue'}) // returns the first occurance
  * of any [XmlElement] where the given attributes/values are found.
  */
  XmlCollection<XmlNode> query(queryOn){
    XmlCollection<XmlNode> list = new XmlCollection._internal();

    if (queryOn is String){
      _queryNameInternal(queryOn, list);
    }else if (queryOn is XmlNodeType){
      _queryNodeTypeInternal(queryOn, list);
    }else if (queryOn is Map){
      _queryAttributeInternal(queryOn, list);
    }

    return list;
  }


  void _queryAttributeInternal(Map aMap, XmlCollection<XmlNode> list){
    bool checkAttribs(){
      var succeed = true;

      //TODO needs better implementation to
      //break out on first false
      aMap.forEach((k, v){
        if (succeed && attributes.containsKey(k)) {
          if (attributes[k] != v) succeed = false;
        }else{
          succeed = false;
        }
      });

      return succeed;
    }

    if (checkAttribs()){
      list.add(this);
      return;
    }else{
      if (hasChildren){
        children
        .allElements()
        .forEach((el){
          if (!list.isEmpty) return;
          el._queryAttributeInternal(aMap, list);
        });
      }
    }
  }

  void _queryNodeTypeInternal(XmlNodeType nodeType,
                              XmlCollection<XmlNode> list){
    if (type == nodeType){
      list.add(this);
      return;
    }else{
      if (hasChildren){
        children
          .forEach((el){
            if (!list.isEmpty) return;
            if (el is XmlElement){
              el._queryNodeTypeInternal(nodeType, list);
            }else{
              if (el.type == nodeType){
                list.add(el);
                return;
              }
            }
          });
      }
    }
  }

  void _queryNameInternal(String tagName, XmlCollection<XmlNode> list){

    if (this.name == tagName){
      list.add(this);
      return;
    }else{
      if (hasChildren){
        children
          .allElements()
          .forEach((el){
            if (!list.isEmpty) return;
            el._queryNameInternal(tagName, list);
          });
      }
    }
  }

  /**
  * Returns a list of nodes in the tree that match the given [queryOn]
  * parameter.
  *
  * ## Usage ##
  * * query('tagName') = returns first occurance matching tag name.
  * * query(XmlNodeType.CDATA) // returns first occurance of element matching
  * the given node type (CDATA node in this example).
  */
  XmlCollection<XmlNode> queryAll(queryOn){
    var list = new XmlCollection<XmlNode>._internal();

    if (queryOn is String){
      _queryAllNamesInternal(queryOn, list);
    }else if (queryOn is XmlNodeType){
      _queryAllNodeTypesInternal(queryOn, list);
    }else if (queryOn is Map){
      _queryAllAttributesInternal(queryOn, list);
    }

    return list;
  }

  void _queryAllAttributesInternal(Map aMap, XmlCollection<XmlNode> list){
    bool checkAttribs(){
      var succeed = true;

      //TODO needs better implementation to
      //break out on first false
      aMap.forEach((k, v){
        if (succeed && attributes.containsKey(k)) {
          if (attributes[k] != v) succeed = false;
        }else{
          succeed = false;
        }
      });

      return succeed;
    }

    if (checkAttribs()){
      list.add(this);
    }else{
      if (hasChildren){
        children
        .allElements()
        .forEach((el){
          el._queryAttributeInternal(aMap, list);
        });
      }
    }
  }

  void _queryAllNodeTypesInternal(XmlNodeType nodeType, XmlCollection<XmlNode> list){
    if (type == nodeType){
      list.add(this);
    }else{
      if (hasChildren){
        children
          .forEach((el){
            if (el is XmlElement){
              el._queryAllNodeTypesInternal(nodeType, list);
            }else{
              if (el.type == nodeType){
                list.add(el);
              }
            }
          });
      }
    }
  }

  _queryAllNamesInternal(String tagName, XmlCollection<XmlNode> list){
    if (this.name == tagName){
      list.add(this);
    }

    if (hasChildren){
      children
      .where((el) => el is XmlElement)
      .forEach((el){
        el._queryAllNamesInternal(tagName, list);
      });
    }
  }
}
