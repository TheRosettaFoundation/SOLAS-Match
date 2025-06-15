#!/usr/bin/env python
# -*- coding: utf-8 -*-

import xml.etree.ElementTree as ET
import sys, getopt

def printUsage():
    print 'usage: mergeTranslation.py --englishFile <englishFile> --originalFile <originalFile> --translatedFile <translatedFile> [-o <output>]'

def main(argv):
    englishFile = ''
    originalFile = ''
    translatedFile = ''
    outputFile = 'merged_strings.xml'
    try:
        opts, args = getopt.getopt(argv, "h", ["englishFile=", "originalFile=", "translatedFile="])
    except getopt.GetoptError:
        printUsage()
        sys.exit(2)
    for opt, arg, in opts:
        if opt == '-h':
            printUsage()
            sys.exit()
        elif opt == '--englishFile':
            englishFile = arg
        elif opt == '--originalFile':
            originalFile = arg
        elif opt == '--translatedFile':
            translatedFile = arg
    if englishFile == '' or originalFile == '' or translatedFile == '':
        printUsage()
        sys.exit(2)
    print "Parsing files..."
    encoding = "UTF-8"
    englishXml = ET.parse(englishFile)
    originalXml = ET.parse(originalFile)
    translatedXml = ET.parse(translatedFile)
    for stringElement in translatedXml.findall("string"):
        xpathQuery = ".//string[@name='" + stringElement.get("name") + "']"
        originalElement = originalXml.getroot().find(xpathQuery)
        englishElement = englishXml.getroot().find(xpathQuery)
        translatedValue = ET.tostring(stringElement, encoding, "text").strip()
        englishValue = ET.tostring(englishElement, encoding, "text").strip()
        if originalElement is None:
            originalValue = ''
        else:
            originalValue = ET.tostring(originalElement, encoding, "text").strip()
        if translatedValue != englishValue and translatedValue != originalValue:
            if originalElement is None:
                originalXml.getroot().append(stringElement)
            else:
                originalElement.text = translatedValue
    locFile = open(originalFile, "w")
    locFile.write(ET.tostring(originalXml.getroot()))
    print "Finished. ", originalFile, " has been merged with ", translatedFile

if __name__ == "__main__":
    main(sys.argv[1:])
