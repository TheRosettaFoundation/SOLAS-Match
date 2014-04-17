part of xml_utils_prujohn;

/**
* Represents a base class for XML nodes.  This node is essentially
* read-only.  Use [XmlElement] for manipulating attributes
* and heirarchies.
*/
class XmlNode {
  final XmlNodeType type;
  XmlElement parent;

  XmlNode(this.type);

  void remove(){
    if (parent == null) return;

    var i = parent.children.indexOf(this);
    if (i == -1){
      throw const XmlException('Element not found.');
    }

    parent.children.removeRange(i, 1);
  }

  /// Returns a text representation of the XmlNode tree.
  String toString() {
    StringBuffer s = new StringBuffer();
    _stringifyInternal(s, this, 0);
    return s.toString();
  }

  static void _stringifyInternal(
                  StringBuffer b, XmlNode n, int indent,
                  {bool leadingWhiteSpace:true}) {
    switch(n.type){
      case XmlNodeType.Element:
        XmlElement el = n as XmlElement;

        if (leadingWhiteSpace) {
          b.write('\r${_space(indent)}');
        }
        b.write('<${el.name}');

        if (el.namespaces.length > 0){
          el.namespaces.forEach((k, v) =>
              b.write(new XmlNamespace(k, v).toString()));
        }

        if (el.attributes.length > 0){
          el.attributes.forEach((k, v) =>
              b.write(new XmlAttribute(k, v).toString()));
        }

        b.write('>');

        if (el.hasChildren) {
          for (int i = 0; i < el.children.length; i++) {
            bool whitespace =
                !(i > 0 && el.children[i-1].type == XmlNodeType.Text);
            _stringifyInternal(
                b, el.children[i], indent + 3, leadingWhiteSpace:whitespace);
          }
        }

        if (el.children.length > 0
            && el.children.last.type != XmlNodeType.Text) {
          b.write('\r${_space(indent)}</${el.name}>');
        } else {
          b.write('</${el.name}>');
        }

        break;
      case XmlNodeType.Namespace:
      case XmlNodeType.Attribute:
        b.write(n.toString());
        break;
      case XmlNodeType.Text:
        b.write('$n');
        break;
      case XmlNodeType.PI:
      case XmlNodeType.CDATA:
        b.write('\r$n');
        break;
      default:
        throw new XmlException("Node Type ${n.type} is not supported.");
    }
  }

  static String _space(int amount) {
    final s = new StringBuffer();
    for (int i = 0; i < amount; i++){
      s.write(' ');
    }
    return s.toString();
   }
}
