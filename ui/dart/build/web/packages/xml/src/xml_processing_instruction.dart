part of xml_utils_prujohn;

/** Represents and XML Processing Instruction (PI) node. */
class XmlProcessingInstruction extends XmlNode {
  final String text;

  XmlProcessingInstruction(this.text) :
    super(XmlNodeType.PI);

  String toString() => '<?\r$text\r?>';
}
