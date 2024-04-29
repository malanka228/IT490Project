#!/bin/bash

version=$1
name=$2
files=$3

tar -czf "${name}_${version}.tar.gz" "$files"

