library app_bootstrap;

import 'package:polymer/polymer.dart';

import 'TaskStream.dart' as i0;

void main() {
  configureForDeployment([
      'TaskStream.dart',
      ]);
  initPolymer();
}