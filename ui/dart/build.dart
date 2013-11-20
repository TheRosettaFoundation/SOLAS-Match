#!/usr/bin/env dart

import 'package:polymer/builder.dart';
import 'dart:io';
import 'dart:async';

void main() {
  lint().then((_) => deploy()).then(compileToJs);
}

compileToJs(_) {
  print("Running dart2js");
  var dart2js = 'dart2js';
  List<Future<ProcessResult>> results = new List<Future<ProcessResult>>();
  print("Compiling home.dart");
  results.add(
    Process.run(dart2js, ['--suppress-warnings', '--suppress-hints', '--minify',
        '-o', 'out/web/Routes/Users/home.dart.js',
        'out/web/Routes/Users/home.dart'], runInShell: true));
  print("Compiling ClaimedTasks.dart");
  results.add(
    Process.run(dart2js, ['--suppress-warnings', '--suppress-hints', '--minify',
        '-o', 'out/web/Routes/Users/ClaimedTasks.dart.js',
        'out/web/Routes/Users/ClaimedTasks.dart'], runInShell: true));
  print("Compiling UserPrivateProfileForm.dart");
  results.add(
    Process.run(dart2js, ['--suppress-warnings', '--suppress-hints', '--minify',
        '-o', 'out/web/Routes/Users/UserPrivateProfile.dart.js',
        'out/web/Routes/Users/UserPrivateProfile.dart'], runInShell: true));
  /*print("Compiling test");
  results.add(
    Process.run(dart2js, ['--suppress-warnings', '--suppress-hints', //'--minify',
        '-o', 'out/web/test.html_bootstrap.dart.js',
        'out/web/test.html_bootstrap.dart'], runInShell: true));*/
  Future.wait(results).then((List<ProcessResult> res) {
    res.forEach((ProcessResult result) {
      print("\nPrinting result -----------------------------------");
      print("Exit code: " + result.exitCode.toString());
      print("Errors: " + result.stderr + "\n");
      print("Output: " + result.stdout + "\n");
      print("-----------------------------------------------------");
    });
    print("Done");
  });
}