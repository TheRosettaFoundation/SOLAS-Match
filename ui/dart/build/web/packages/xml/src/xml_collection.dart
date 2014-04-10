part of xml_utils_prujohn;

/**
* Represents a queryable collection of [XmlNode] elements.
*/
class XmlCollection<E extends XmlNode> extends ListBase<E> {
  final List<E> _collection = new List<E>();

  XmlCollection._internal();

  XmlCollection._from(Iterable<E> iterable) {
    _collection.addAll(iterable);
  }

  /**
   * Returns the element at the given [index] in the list or throws
   * an [RangeError] if [index] is out of bounds.
   */
  E operator [](int index) => _collection[index];

  /**
   * Sets the entry at the given [index] in the list to [value].
   * Throws an [RangeError] if [index] is out of bounds.
   */
  void operator []=(int index, E value) {
    _collection[index] = value;
  }

  int get length => _collection.length;

  /**
   * Changes the length of the list. If [newLength] is greater than
   * the current [length], entries are initialized to [:null:]. Throws
   * an [UnsupportedError] if the list is not extendable.
   */
  void set length(int newLength) {
    _collection.length = newLength;
  }

  /**
   * Adds value at the end of the list, extending the length by one. Throws
   * an UnsupportedError if the list is not extendable.
   *
   * Deprecated: Use add instead.
   */
  void addLast(E value) => _collection.add(value);

  /**
   * Adds [value] at the end of the list, extending the length by
   * one. Throws an [UnsupportedError] if the list is not
   * extendable.
   */
  void add(XmlNode value) => _collection.add(value);

  /**
   * Appends all elements of the [iterable] to the end of this list.
   * Extends the length of the list by the number of elements in [iterable].
   * Throws an [UnsupportedError] if this list is not extensible.
   */
  void addAll(Iterable<E> iterable) => _collection.addAll(iterable);

  /**
   * Returns a new list containing the elemenst from [start] to [end].
   *
   * If [end] is omitted, the [length] of the list is used.
   *
   * It is an error if [start] or [end] are not list indices for this list,
   * or if [end] is before [start].
   */
  List<E> sublist(int start, [int end]) => _collection.sublist(start, end);

  /**
   * Returns a reversed fixed-length view of this [List].
   *
   * The reversed list has elements in the opposite order of this list.
   * It is backed by this list, but will stop working if this list
   * becomes shorter than its current length.
   */
  List<E> get reversed => _collection.reversed;

  /**
   * Sorts the list according to the order specified by the [compare] function.
  *
   * The [compare] function must act as a [Comparator].
   * The default [List] implementations use [Comparable.compare] if
   * [compare] is omitted.
   */
  void sort([int compare(E a, E b)]) {
    _collection.sort(compare);
  }

  /**
   * Returns a lazy [Iterable] where each element [:e:] of [this] is replaced
   * by the result of [:f(e):].
   *
   * This method returns a view of the mapped elements. As long as the
   * returned [Iterable] is not iterated over, the supplied function [f] will
   * not be invoked. The transformed elements will not be cached. Iterating
   * multiple times over the the returned [Iterable] will invoke the supplied
   * function [f] multiple times on the same element.
   */
  Iterable map(f(E element)) => _collection.map(f);

  /**
   * Expand each element of this [Iterable] into zero or more elements.
   *
   * The resulting Iterable will run through the elements returned
   * by [f] for each element of this, in order.
   *
   * The returned [Iterable] is lazy, and will call [f] for each element
   * of this every time it's iterated.
   */
  Iterable expand(Iterable f(E element)) => _collection.expand(f);

  /**
  * Returns an unmodifiable Map view of this.
  *
  * It has the indices of this list as keys, and the corresponding elements
  * as values.
  */
  Map<int, E> asMap() => _collection.asMap();

  /**
   * Returns the first index of [element] in the list.
   *
   * Searches the list from index [start] to the length of the list.
   * The first time an element [:e:] is encountered so that [:e == element:],
   * the index of [:e:] is returned.
   * Returns -1 if [element] is not found.
   */
  int indexOf(E element, [int start = 0]) =>
      _collection.indexOf(element, start);

  /**
   * Returns the last index of [element] in the list.
  *
   * Searches the list backwards from index [start] (inclusive) to 0.
   * The first time an element [:e:] is encountered so that [:e == element:],
   * the index of [:e:] is returned.
   * If start is not provided, it defaults to [:this.length - 1:] .
   * Returns -1 if [element] is not found.
   */
  int lastIndexOf(E element, [int start]) =>
      _collection.lastIndexOf(element, start);

  /**
   * Removes all elements in the list.
  *
   * The length of the list becomes zero.
   * Throws an [UnsupportedError], and retains all elements, if the
   * length of the list cannot be changed.
   */
  void clear() {
    _collection.clear();
  }

  /**
   * Removes the element at position[index] from the list.
  *
   * This reduces the length of the list by one and moves all later elements
   * down by one position.
   * Returns the removed element.
   * Throws an [ArgumentError] if [index] is not an [int].
   * Throws an [RangeError] if the [index] does not point inside
   * the list.
   * Throws an [UnsupportedError], and doesn't remove the element,
   * if the length of the list cannot be changed.
   */
  E removeAt(int index) => _collection.removeAt(index);

  /**
   * Pops and returns the last element of the list.
   * Throws a [UnsupportedError] if the length of the
   * list cannot be changed.
   */
  E removeLast() => _collection.removeLast();

  /**
   * Copies [length] elements of [from], starting
   * at [startFrom], into the list, starting at [start].
   * If [length] is 0, this method does not do anything.
   * Throws an [ArgumentError] if [length] is negative.
   * Throws an [RangeError] if [start] or
   * [:start + length - 1:] are out of range for [:this:], or if
   * [startFrom] or [:startFrom + length - 1:] are out of range for [from].
   */
  void setRange(int start, int length, List<E> from, [int startFrom]) {
    _collection.setRange(start, length, from, startFrom);
  }

  /**
   * Removes [length] elements from the list, beginning at [start].
   * Throws an [UnsupportedError] if the list is
   * not extendable.
   * If [length] is 0, this method does not do anything.
   * Throws an [ArgumentError] if [length] is negative.
   * Throws an [RangeError] if [start] or
   * [:start + length: - 1] are out of range.
   */
  void removeRange(int start, int length) {
    _collection.removeRange(start, length);
  }

  /**
   * Inserts a new range into the list, starting from [start] to
   * [:start + length - 1:]. The entries are filled with [fill].
   * Throws an [UnsupportedError] if the list is
   * not extendable.
   * If [length] is 0, this method does not do anything.
   * If [start] is the length of the list, this method inserts the
   * range at the end of the list.
   * Throws an [ArgumentError] if [length] is negative.
   * Throws an [RangeError] if [start] is negative or if
   * [start] is greater than the length of the list.
   */
  void insertRange(int start, int length, [E fill]) {
    _collection.fillRange(start, length, fill);
  }


  // From [Collection].

  /**
   * Adds an element to this collection.
   */
  //void add(E element) => _collection.add(element);

  /**
   * Adds all of [elements] to this collection.
  *
   * Equivalent to adding each element in [elements] using [add],
   * but some collections may be able to optimize it.
   */
  //void addAll(Iterable<E> elements) => _collection.addAll(elements);

  /**
   * Removes an instance of [element] from this collection.
  *
   * This removes only one instance of the element for collections that can
   * contain the same element more than once (e.g., [List]). Which instance
   * is removed is decided by the collection.
  *
   * Has no effect if the elements is not in this collection.
   */
  bool remove(E element) => _collection.remove(element);

  /**
   * Removes all of [elements] from this collection.
  *
   * Equivalent to calling [remove] once for each element in
   * [elements], but may be faster for some collections.
   */
  void removeAll(Iterable elements) {
    for (var element in elements) {
      _collection.remove(element);
    }
  }

  /**
   * Removes all elements of this collection that are not
   * in [elements].
  *
   * For [Set]s, this is the intersection of the two original sets.
   */
  void retainAll(Iterable elements) {
    _collection.retainWhere((e) => elements.contains(e));
  }

  // From [Iterable].

  /**
   * Returns an [Iterator] that iterates over this [Iterable] object.
   */
  Iterator<E> get iterator => _collection.iterator;

  /**
    * Returns a lazy [Iterable] with all elements that satisfy the
    * predicate [f].
    *
    * This method returns a view of the mapped elements. As long as the
    * returned [Iterable] is not iterated over, the supplied function [f] will
    * not be invoked. Iterating will not cache results, and thus iterating
    * multiple times over the the returned [Iterable] will invoke the supplied
    * function [f] multiple times on the same element.
    */
  Iterable<E> where(bool f(E element)) => _collection.where(f);

  /**
   * Check whether the collection contains an element equal to [element].
   */
  bool contains(E element) => _collection.contains(element);

  /**
   * Applies the function [f] to each element of this collection.
   */
  void forEach(void f(E element)) {
    _collection.forEach(f);
  }

  /**
   * Fold a collection to a single value by iteratively combining each element
   * of the collection with an existing value using the provided function.
   * Use [initialValue] as the initial value, and the function [combine] to
   * create a new value from the previous one and an element.
   *
   * Example of calculating the sum of a collection:
   *
   *   collection.fold(0, (prev, element) => prev + element);
   */
  dynamic fold(var initialValue,
               dynamic combine(var previousValue, E element)) =>
      _collection.fold(initialValue, combine);

  /**
   * Reduces a collection to a single value by iteratively combining elements
   * of the collection using the provided function.
   *
   * Example of calculating the sum of an iterable:
   *
   *     iterable.reduce((value, element) => value + element);
   *
   */
  dynamic reduce(dynamic combine(var previousValue, E element)) =>
      _collection.reduce(combine);

  /**
   * Returns true if every elements of this collection satisify the
   * predicate [f]. Returns false otherwise.
   */
  bool every(bool f(E element)) => _collection.every(f);

  /**
   * Convert each element to a [String] and concatenate the strings.
   *
   * Converts each element to a [String] by calling [Object.toString] on it.
   * Then concatenates the strings, optionally separated by the [separator]
   * string.
   */
  String join([String separator]) => _collection.join(separator);

  /**
   * Returns true if one element of this collection satisfies the
   * predicate [f]. Returns false otherwise.
   */
  bool any(bool f(E element)) => _collection.any(f);

  List<E> toList({growable : true}) => new List<E>.from(this);
  Set<E> toSet() => new Set<E>.from(this);

  /**
   * Returns true if there is no element in this collection.
   */
  bool get isEmpty => _collection.isEmpty;

  /**
   * Returns an [Iterable] with at most [n] elements.
   *
   * The returned [Iterable] may contain fewer than [n] elements, if [this]
   * contains fewer than [n] elements.
   */
  Iterable<E> take(int n) => _collection.take(n);

  /**
   * Returns an [Iterable] that stops once [test] is not satisfied anymore.
   *
   * The filtering happens lazily. Every new [Iterator] of the returned
   * [Iterable] will start iterating over the elements of [this].
   * When the iterator encounters an element [:e:] that does not satisfy [test],
   * it discards [:e:] and moves into the finished state. That is, it will not
   * ask or provide any more elements.
   */
  Iterable<E> takeWhile(bool test(E value)) => _collection.takeWhile(test);

  /**
   * Returns an [Iterable] that skips the first [n] elements.
   *
   * If [this] has fewer than [n] elements, then the resulting [Iterable] will
   * be empty.
   */
  Iterable<E> skip(int n) => _collection.skip(n);

  /**
   * Returns an [Iterable] that skips elements while [test] is satisfied.
   *
   * The filtering happens lazily. Every new [Iterator] of the returned
   * [Iterable] will iterate over all elements of [this].
   * As long as the iterator's elements do not satisfy [test] they are
   * discarded. Once an element satisfies the [test] the iterator stops testing
   * and uses every element unconditionally.
   */
  Iterable<E> skipWhile(bool test(E value)) => _collection.skipWhile(test);

  /**
   * Returns the first element.
   *
   * If [this] is empty throws a [StateError]. Otherwise this method is
   * equivalent to [:this.elementAt(0):]
   */
  E get first => _collection.first;

  /**
   * Returns the last element.
   *
   * If [this] is empty throws a [StateError].
   */
  E get last => _collection.last;

  /**
   * Returns the single element in [this].
   *
   * If [this] is empty or has more than one element throws a [StateError].
   */
  E get single => _collection.single;

  /**
   * Returns the [index]th element.
   *
   * If [this] [Iterable] has fewer than [index] elements throws a
   * [RangeError].
   *
   * Note: if [this] does not have a deterministic iteration order then the
   * function may simply return any element without any iteration if there are
   * at least [index] elements in [this].
   */
  E elementAt(int index) => _collection.elementAt(index);

  /**
   * Returns the first element that satisfies the given predicate [f].
   *
   * If none matches, the result of invoking the [orElse] function is
   * returned. By default, when [orElse] is `null`, a [StateError] is
   * thrown.
   */
  E firstWhere(bool test(E value), { E orElse() }) =>
      _collection.firstWhere(test, orElse:orElse);

  /**
   * Returns the last element that satisfies the given predicate [f].
   *
   * If none matches, the result of invoking the [orElse] function is
   * returned. By default, when [orElse] is [:null:], a [StateError] is
   * thrown.
   */
  E lastWhere(bool test(E value), {E orElse()}) =>
      _collection.lastWhere(test, orElse:orElse);

  /**
   * Returns the single element that satisfies [f]. If no or more than one
   * element match then a [StateError] is thrown.
   */
  E singleWhere(bool test(E value)) => _collection.singleWhere(test);

  /**
   * Removes all elements of this collection that satisfy [test].
   *
   * An elements [:e:] satisfies [test] if [:test(e):] is true.
   */
  void removeWhere(bool test(E element)) => _collection.removeWhere(test);

  /**
   * Removes all elements of this collection that fail to satisfy [test].
   *
   * An elements [:e:] satisfies [test] if [:test(e):] is true.
   */
  void retainWhere(bool test(E element)) => _collection.retainWhere(test);

  /**
   * Inserts the element at position [index] in the list.
   *
   * This increases the length of the list by one and shifts all later elements
   * towards the end of the list.
   *
   * It is an error if the [index] does not point inside the list or at the
   * position after the last element.
   */
  void insert(int index, E element) => _collection.insert(index, element);



//  /**
//   * Check whether the collection contains an element equal to [element].
//   */
//  bool contains(E element) => _collection.contains(element);
//
//  /**
//   * Returns the last element of the [XmlCollection], or throws an out of bounds
//   * exception if the [XmlCollection] is empty.
//   */
//  E get last => _collection.last;
//
//  /**
//   * Returns the first index of [element] in this [XmlCollection].
//   * Searches this [XmlCollection] from index [start] to the length of the
//   * [XmlCollection]. Returns -1 if [element] is not found.
//   */
//  indexOf(E element) => _collection.indexOf(element);
//
//
//  /**
//   * Reduce a collection to a single value by iteratively combining each element
//   * of the collection with an existing value using the provided function.
//   * Use [initialValue] as the initial value, and the function [combine] to
//   * create a new value from the previous one and an element.
//   *
//   * Example of calculating the sum of a collection:
//   *
//   *   collection.reduce(0, (prev, element) => prev + element);
//   */
//  dynamic reduce(dynamic initialValue,
//                 dynamic combine(dynamic previousValue, E element))
//  => _collection.reduce(initialValue, combine);
//
//
//
//  /**
//   * Returns the element at the given [index] in the [XmlCollection] or throws
//   * an [IndexOutOfRangeException] if [index] is out of bounds.
//   */
//  E operator [](int index) => _collection[index];
//
//  void _add(E element) => _collection.add(element);
//
//  void _removeRange(int start, int length) =>
//      _collection.removeRange(start, length);
//
//  /**
//   * Applies the function [f] to each element of this collection.
//   */
//  void forEach(void f(E element)) => _collection.forEach(f);
//
//  /**
//   * Returns a new [XmlCollection] with the elements [: f(e) :]
//   * for each element [e] of this collection.
//   *
//   * Note on typing: the return type of f() could be an arbitrary
//   * type and consequently the returned collection's
//   * typeis Collection.
//   */
//  XmlCollection map(f(E element)) =>
//      new XmlCollection._from(_collection.mappedBy(f).toList());
//
//  /**
//   * Returns a new [XmlCollection] with the elements of this collection
//   * that satisfy the predicate [f].
//   *
//   * An element satisfies the predicate [f] if [:f(element):]
//   * returns true.
//   */
//  XmlCollection<E> filter(bool f(E element))
//  => new XmlCollection._from(_collection.where(f).toList());
//

  XmlCollection<XmlElement> allElements() =>
      new XmlCollection._from(_collection.where((n) => n is XmlElement));

  /**
  * Returns the first node in the tree that matches the given [queryOn]
  * parameter.
  *
  * ## Usage ##
  * * query('tagName') // returns first occurance matching tag name.
  * * query(XmlNodeType.CDATA) // returns first occurance of element matching
  * the given node type (CDATA node in this example).
  * * query({'attributeName':'attributeValue'}) // returns the first occurance
  * of any [XmlElement] where the given attributes/values are found.
  */
  XmlCollection<E> query(queryOn){
    XmlCollection<E> list = new XmlCollection._internal();

    if (queryOn is String){
      for (final node in this.allElements()){
        _queryNameInternal(queryOn, list, node);
        if (!list.isEmpty) break;
      }
    }else if (queryOn is XmlNodeType){
      for (final node in this){
        _queryNodeTypeInternal(queryOn, list, node);
        if (!list.isEmpty) break;
      }
    }else if (queryOn is Map){
      for (final node in this.allElements()){
        _queryAttributeInternal(queryOn, list, node);
        if (!list.isEmpty) break;
      }
    }

    return list;
  }


  void _queryAttributeInternal(Map aMap,
                               XmlCollection<E> list,
                               XmlElement n){
    bool checkAttribs(){
      var succeed = true;

      //TODO needs better implementation to
      //break out on first false
      aMap.forEach((k, v){
        if (succeed && n.attributes.containsKey(k)) {
          if (n.attributes[k] != v) succeed = false;
        }else{
          succeed = false;
        }
      });

      return succeed;
    }

    if (checkAttribs()){
      list.add(n);
      return;
    }else{
      if (n.hasChildren){
        n.children
        .allElements()
        .forEach((el){
          if (!list.isEmpty) return;
          (el as XmlElement)._queryAttributeInternal(aMap, list);
        });
      }
    }
  }

  void _queryNodeTypeInternal(XmlNodeType nodeType,
                              XmlCollection<E> list,
                              XmlNode node){
    if (node.type == nodeType){
      list.add(node);
      return;
    }else{
      if ((node as XmlElement).hasChildren){
        (node as XmlElement).children
          .forEach((el){
            if (!list.isEmpty) return;
            if (el is XmlElement){
              el._queryNodeTypeInternal(nodeType, list);
            }else{
              if (el.type == nodeType){
                list.add(el);
                return;
              }
            }
          });
      }
    }
  }

  void _queryNameInternal(String tagName, XmlCollection<E> list,
                          XmlElement element){

    if (element.name == tagName){
      list.add(element);
      return;
    }else{
      if (element.hasChildren){
        element.children
          .allElements()
          .forEach((el){
            if (!list.isEmpty) return;
            el._queryNameInternal(tagName, list);
          });
      }
    }
  }

  /**
  * Returns a list of nodes in the tree that match the given [queryOn]
  * parameter.
  *
  * ## Usage ##
  * * query('tagName') = returns first occurance matching tag name.
  * * query(XmlNodeType.CDATA) // returns first occurance of element matching
  * the given node type (CDATA node in this example).
  */
  XmlCollection<E> queryAll(queryOn){
    var list = new XmlCollection<E>._internal();

    if (queryOn is String){
      for (final node in this.allElements()){
        _queryAllNamesInternal(queryOn, list, node);
      }
    }else if (queryOn is XmlNodeType){
      for (final node in this){
        _queryAllNodeTypesInternal(queryOn, list, node);
      }
    }else if (queryOn is Map){
      for (final node in this.allElements()){
        _queryAllAttributesInternal(queryOn, list, node);
      }
    }

    return list;
  }

  void _queryAllAttributesInternal(Map aMap,
                                   XmlCollection<E> list,
                                   XmlElement element){
    bool checkAttribs(){
      var succeed = true;

      //TODO needs better implementation to
      //break out on first false
      aMap.forEach((k, v){
        if (succeed && element.attributes.containsKey(k)) {
          if (element.attributes[k] != v) succeed = false;
        }else{
          succeed = false;
        }
      });

      return succeed;
    }

    if (checkAttribs()){
      list.add(element);
    }else{
      if (element.hasChildren){
        element.children
        .allElements()
        .forEach((el){
          el._queryAttributeInternal(aMap, list);
        });
      }
    }
  }

  void _queryAllNodeTypesInternal(XmlNodeType nodeType,
                                  XmlCollection<E> list,
                                  XmlNode node){
    if (node.type == nodeType){
      list.add(node);
    }else{
      if ((node as XmlElement).hasChildren){
        (node as XmlElement).children
          .forEach((el){
            if (el is XmlElement){
              el._queryAllNodeTypesInternal(nodeType, list);
            }else{
              if (el.type == nodeType){
                list.add(el);
              }
            }
          });
      }
    }
  }

  void _queryAllNamesInternal(String tagName,
                         XmlCollection<E> list,
                         XmlElement element){
    if (element.name == tagName){
      list.add(element);
    }

    if (element.hasChildren){
      element.children
      .where((el) => el is XmlElement)
      .forEach((el){
        el._queryAllNamesInternal(tagName, list);
      });
    }
  }
}
