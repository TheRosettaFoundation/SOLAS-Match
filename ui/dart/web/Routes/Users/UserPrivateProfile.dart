library app_bootstrap;

import 'package:polymer/polymer.dart';
import "../../lib/Loader.dart";

import 'UserPrivateProfileForm.dart' as i0;

void main() {
  configureForDeployment([
      'UserPrivateProfileForm.dart'
      ]);
  Loader.load().then((_) => initPolymer());
}