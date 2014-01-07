library app_bootstrap;

import 'package:polymer/polymer.dart';
import "../../lib/SolasMatchDart.dart";

import 'ClaimedTasksStream.dart' as i0;

void main() {
  configureForDeployment([
      'ClaimedTasksStream.dart',
      ]);
  Loader.load().then((_) => initPolymer());
}
