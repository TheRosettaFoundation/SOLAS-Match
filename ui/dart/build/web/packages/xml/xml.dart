// A lightweight, XML parser and emitter.
// See README.md for more info on features and limitations.

library xml_utils_prujohn;

import 'dart:collection';

part 'src/xml_element.dart';
part 'src/xml_parser.dart';
part 'src/xml_tokenizer.dart';
part 'src/xml_node.dart';
part 'src/xml_node_type.dart';
part 'src/xml_text.dart';
part 'src/xml_attribute.dart';
part 'src/xml_exception.dart';
part 'src/xml_cdata.dart';
part 'src/xml_processing_instruction.dart';
part 'src/xml_collection.dart';
part 'src/xml_namespace.dart';

/**
* Utility class to work with XML data.
*/
class XML {

  /**
  * Returns a [XmlElement] tree representing the raw XML fragment [String].
  *
  * Optional parameter [withQuirks] will allow the following when set to true:
  *
  * * Optional quotes for simple attribute values (no spaces).
  */
  static XmlElement parse(String xml, [withQuirks = false]) =>
      XmlParser._parse(xml.trim(), withQuirks);

  /**
  * Returns a stringified version of an [XmlElement] tree.
  * You can also call .toString() on any [XmlElement].
  */
  static String stringify(XmlElement element) => element.toString();
}
