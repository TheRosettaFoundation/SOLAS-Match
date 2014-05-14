
import 'package:polymer/polymer.dart';
import 'dart:html';
import "../lib/SolasMatchDart.dart";

@CustomTag('auto-complete')
class AutoCompleteElement extends PolymerElement {
  @observable String search;
  final List<String> results = toObservable([]);
  final List<String> haystack = [];
  bool skipSearch = false;
  int keyboardSelect = -1;

  AutoCompleteElement.created() : super.created() {
    
    Loader.load()
    .then((_) {
      onPropertyChange(this, #search, _performSearch);
    });
  }

  void select(Event e, var detail, Node target) {
    search = target.text;
    _reset();
    skipSearch = true;
  }

  _performSearch() {
    if (skipSearch) {
      skipSearch = false;
      return;
    }
    results.clear();
    if (search.trim().isEmpty) return;
    
    String lower = search.toLowerCase();
    CountryDao.getCountriesByPattern(search)
    .then((List<String> countries) {
      results.addAll(countries);
    });
    
  }

  keyup(KeyboardEvent e, var detail, Node target) {
    switch (new KeyEvent.wrap(e).keyCode) {
      case KeyCode.ESC:
        _clear();
        break;
      case KeyCode.UP:
        _moveUp();
        break;
      case KeyCode.DOWN:
        _moveDown();
        break;
      case KeyCode.ENTER:
        _select();
        break;
    }
  }

  _moveDown() {
    List<Element> lis = shadowRoot.querySelectorAll('ul li');
    if (keyboardSelect >= 0) lis[keyboardSelect].classes.remove('selecting');
    keyboardSelect = ++keyboardSelect == lis.length ? 0 : keyboardSelect;
    lis[keyboardSelect].classes.add('selecting');
  }

  _moveUp() {
    List<Element> lis = shadowRoot.querySelectorAll('ul li');
    if (keyboardSelect >= 0) lis[keyboardSelect].classes.remove('selecting');
    if (keyboardSelect == -1) keyboardSelect = lis.length;
    keyboardSelect = --keyboardSelect == -1 ? lis.length-1 : keyboardSelect;
    lis[keyboardSelect].classes.add('selecting');
  }

  _clear() {
    _reset();
    search = '';
    skipSearch = true;
  }

  _select() {
    List<Element> lis = shadowRoot.querySelectorAll('ul li');
    search = lis[keyboardSelect].text;
    skipSearch = true;
    _reset();
  }

  _reset() {
    keyboardSelect = -1;
    results.clear();
  }
}