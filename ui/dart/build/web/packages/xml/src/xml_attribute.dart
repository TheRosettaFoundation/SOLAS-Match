part of xml_utils_prujohn;

/**
* Represents an attribute component of an XML element.
*/
class XmlAttribute extends XmlNode
{
  final String name;
  final String value;

  XmlAttribute(this.name, this.value)
  :
    super(XmlNodeType.Attribute);

  String toString() =>
      value.contains('"') ? " $name='$value'" : ' $name="$value"';
}