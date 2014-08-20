library app_bootstrap;

import 'package:polymer/polymer.dart';
import "../../lib/SolasMatchDart.dart";

import 'ProjectAlterForm.dart' as i0;

void main() {
  configureForDeployment([
      'ProjectAlterForm.dart'
      ]);
  Loader.load().then((_) => initPolymer());
}
