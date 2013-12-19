library app_bootstrap;

import 'package:polymer/polymer.dart';
import "../../lib/Loader.dart";

import 'ProjectCreateForm.dart' as i0;

void main() {
  configureForDeployment([
      'ProjectCreateForm.dart',
      ]);
  Loader.load().then((_) => initPolymer());
}
