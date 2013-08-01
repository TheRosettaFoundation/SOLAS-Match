import 'package:web_ui/component_build.dart';
import 'dart:io';

// Ref: http://www.dartlang.org/articles/dart-web-components/tools.html
main() {
  //build(new Options().arguments, ['web/test.html']);
  var args = new List.from(new Options().arguments);
  args.addAll(['--', '--basedir', 'web/', '--out', 'web/compiled']);
  // Uncomment to build web components
  //build(args, ['web/components/TaskStream.html', 'web/components/UserPrivateProfileForm.html']);
  //build(args, ['web/components/ProjectCreateForm.html']);
}
