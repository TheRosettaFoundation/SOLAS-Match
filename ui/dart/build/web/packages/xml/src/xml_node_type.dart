part of xml_utils_prujohn;

/**
* Enumerates [XmlNode] types.
*/
class XmlNodeType {
  final String _type;

  const XmlNodeType(this._type);

  static const Element = const XmlNodeType('Element');
  static const Attribute = const XmlNodeType('Attribute');
  static const Text = const XmlNodeType('Text');
  static const Namespace = const XmlNodeType('Namespace');
  static const CDATA = const XmlNodeType('CDATA');
  static const PI = const XmlNodeType('PI'); //Processing Instruction

  String toString() => _type;
}