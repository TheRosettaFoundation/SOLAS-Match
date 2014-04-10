part of xml_utils_prujohn;

/**
* Represents a CDATA node of an XML tree.
*/
class XmlCDATA extends XmlNode
{
  final String text;

  XmlCDATA(this.text)
  :
    super(XmlNodeType.CDATA);

  String toString() =>'<![CDATA[\r$text\r]]>';
}
