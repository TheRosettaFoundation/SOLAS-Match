part of xml_utils_prujohn;

/**
* Represents an namespace declaration of an XML element.
*/
class XmlNamespace extends XmlNode {
  final String name;
  final String uri;

  XmlNamespace(this.name, this.uri)
  :
    super(XmlNodeType.Namespace);

  String toString(){
    if (name.isEmpty){
      return uri.contains('"') ? " xmlns$name='$uri'" : ' xmlns$name="$uri"';
    }
    return uri.contains('"') ? " xmlns:$name='$uri'" : ' xmlns:$name="$uri"';
  }
}