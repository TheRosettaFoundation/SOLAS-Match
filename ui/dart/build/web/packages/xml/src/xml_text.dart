part of xml_utils_prujohn;

/**
* Represents a text node component of XML.
*/
class XmlText extends XmlNode {
  final String text;

  XmlText(this.text) :
    super(XmlNodeType.Text);

  String toString() => text;
}