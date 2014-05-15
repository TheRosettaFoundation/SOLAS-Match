library app_bootstrap;

import 'package:polymer/polymer.dart';
import "../lib/SolasMatchDart.dart";

import 'autocomplete.dart' as i0;

void main() {
  configureForDeployment([
      'autocomplete.dart'
      ]);
  Loader.load().then((_) => initPolymer());
}
