
from pyquery import PyQuery as pq
import sys
from pathlib import Path, PurePath
from io import TextIOWrapper
from tempfile import TemporaryFile
import re


class Transformer(object):
    def __init__(self, input=None, output=None, inplace=False):
        self.input = input
        self.output = output

        self.inplace = inplace
        self.future_output = None

    def init(self):
        if self.input is None:
            self.input = sys.stdin
        elif isinstance(self.input, TextIOWrapper):
            if self.inplace:
                self.future_output = self.input.name
        elif isinstance(self.input, str) or isinstance(self.input, PurePath):
            if self.inplace:
                self.future_output = self.input
            self.input = open(self.input, "r")
        else:
            raise ValueError("Type {} not supported as output for {}".format(
                type(self.input), type(self))
            )
        if self.inplace and not self.future_output:
            raise ValueError('inplace mode not supported for this input type')

        if self.inplace:
            self.output = TemporaryFile(mode="w+")
        else:
            if self.output is None:
                self.output = sys.stdout
            elif isinstance(self.output, TextIOWrapper):
                pass
            elif isinstance(self.output, str) or isinstance(self.output, PurePath):
                self.output = open(self.output, "w+")
            else:
                raise ValueError("Type {} not supported as output for {}".format(
                    type(self.output), type(self))
                )

    def transform(self):
        raise NotImplementedError

    def __enter__(self):
        self.init()
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        if self.input not in [sys.stdout, sys.stderr, sys.stdin, None]:
            self.input.close()
        if self.inplace:
            future = open(self.future_output, 'w+')
            self.output.seek(0)
            future.write(self.output.read())
            future.close()
        if self.output not in [sys.stdout, sys.stderr, sys.stdin, None]:
            self.output.close()
        return False


class Prefix(Transformer):
    def __init__(self, prefix='_', *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.prefix = prefix

    def transform(self):
        for line in self.input:
            self.output.write(self.prefix + line)


class AddDetailsTag(Transformer):
    def transform(self):
        html = self.input.read()
        d = pq(html)
        functions = d('dl.function')
        attributes = d('dl.attribute')
        classes = d('dl.class')
        for queries in [functions, attributes, classes]:
            for i in range(len(queries)):
                query = queries.eq(i)
                if not query.html():
                    continue

                summary = '<summary style="list-style-type: none;">' + query('dt').eq(0).outerHtml() + '</summary>'
                query('dt').eq(0).replace_with(summary)
                inner_html = query.html()

                query.html('<details>' + inner_html + '</details>')

        self.output.write(str(d))


class FixHtml(Transformer):
    def transform(self):
        self.output.write('<!doctype html>\n\n')
        regexp = re.compile(r'<script(.*)/>')
        for line in self.input:
            matches = regexp.search(line)
            if matches:
                self.output.write('<script' + matches.groups()[0] + '></script>\r\n')
            else:
                self.output.write(line)


if __name__ == "__main__":
    root = Path(".")
    directory = root / '_build/html'

    def deep_search(path):
        for element in path.iterdir():
            if element.is_file():
                yield element
            if element.is_dir():
                yield from deep_search(element)

    for file in deep_search(directory):
        if file.suffix != ".html":
            continue
        with AddDetailsTag(input=file, inplace=True) as transformer:
            transformer.transform()
        with FixHtml(input=file, inplace=True) as transformer:
            transformer.transform()
