
import argparse
import json
import sys
import csv
from layout import Table1ArgZone_Main, Table2ArgZone_Main
import copy
from simpleodspy.sodsspreadsheet import SodsSpreadSheet  # type: ignore
from simpleodspy.sodsods import SodsOds  # type: ignore
import os
import tempfile
from pylatex import Document, LongTable, MultiColumn, Tabular, TextColor  # type: ignore
import subprocess
import traceback
from metazone import MetaCell, SuperCell, MetaZone
from typing import Tuple, List, Optional


def eprint(*args, **kwargs):
    """
    Function to print to the standard error output
    :param args: same positional arguments than the standard print function
    :param kwargs: same keyword arguments than the standard print function
    :return:
    """
    print(*args, file=sys.stderr, **kwargs)


def update_dictionary(dic1: dict, dic2: dict) -> dict:
    """
    Recursively update a dictionary with a second dictionary
    (upgrade of the dict.update method)
    :param dic1: dictionary to update
    :param dic2: dictionary to be added
    :return: A recursive fusion of dic1 and dic2
    """
    for key in dic2:
        if key in dic1:
            if isinstance(dic1[key], dict):
                update_dictionary(dic1[key], dic2[key])
            else:
                dic1[key] = dic2[key]
        else:
            dic1[key] = dic2[key]
    return dic1


def colnum_string(n):
    """
    Give the column string id of an excel column, based on its number

    >>> colnum_string(1)
    'A'
    >>> colnum_string(2)
    'B'
    >>> colnum_string(26)
    'Z'
    >>> colnum_string(27)
    'AA'
    >>> colnum_string(17049)
    'YES'

    :param n: Number of the column (numbering starts from 1)
    :return: String id of the column in a Excel sheet
    """
    string = ""
    while n > 0:
        n, remainder = divmod(n - 1, 26)
        string = chr(65 + remainder) + string
    return string


def coordinates_to_noob_system(coordinates):
    """
    Compute the Excel coordinates of a cell given its (row, col) coordinates

    >>> coordinates_to_noob_system((0, 0))
    'A1'
    >>> coordinates_to_noob_system((45, 127))
    'DX46'

    :param coordinates: (row, col) coordinates of the cell (numbering starts from 0)
    :return: Excel coordinates of
    """
    row, col = coordinates
    res = colnum_string(col + 1)
    res += str(row + 1)
    return res


"""
Format of the Json dictionnary:
    {
        'tableContent': {
            'valueOriginal': (JSON),
            'valueDecimal': (JSON),
            'spec': (JSON)
        },
        # 'nameToBase': (JSON)
        'exportFormat': (string),
        'exportOptions': {
            ...
    }
"""


class Exporter:
    """
    A Generic class for exporting a table toward a specific file format.
    """
    def __init__(self, table: MetaZone, exportOptions: dict):
        """
        :param table: A dictionary representing the table (as provided by the JSON output of the interface)
        :param exportOptions: A dictionary of export options
        """
        self.table = table
        self.exportOptions = exportOptions

    def process_cell(self, i: int, j: int, cell: MetaCell = None):
        """
        Add the cell at coordinates (i, j) to the document
        :param i: row of the cell
        :param j: column of the cell
        :param cell: a MetaCell object
        :return:
        """
        raise NotImplementedError

    def process_supercell(self, i: int, j: int, supercell: SuperCell = None):
        """
        Add the super cell at coordinates (i, j) to the document
        :param i: row of the super cell
        :param j: column of the super cell
        :param supercell: a SuperCell object
        :return:
        """
        raise NotImplementedError

    def process_header_supercell(self, i: int, j: int, supercell: SuperCell = None):
        """
        Add the specified header super cell to the document
        :param i: row of the header super cell
        :param j: column of the header super cell
        :param supercell: a SuperCell object
        :return:
        """
        raise NotImplementedError

    def end_line(self):
        """
        Add an end of row to the document
        :return:
        """
        raise NotImplementedError

    def end_table(self):
        """
        Add an end of table to the document, and then write the document
        (in binary format if needed) to the standard output
        :return:
        """
        raise NotImplementedError

    def process_table(self):
        """
        Process the whole table, calling the adequate sub-function to process cells, supercells,
        headers, end lines, etc...
        :return:
        """
        output_spec = self.table.spec
        if self.exportOptions['export-multipleCells']:
            for i in range(self.table.R):
                for j in range(self.table.C):
                    self.process_cell(i, j, table.grid[i][j])
                self.end_line()
        else:
            delta = 0
            if self.exportOptions['export-headers']:
                delta = 1

            widths = []
            if len(output_spec['args']) == 2:
                widths = [output_spec['args'][0]['ncells']] + ([output_spec['cwidth']] * output_spec['args'][1]['nsteps'])
                if delta == 1:
                    widths = [1] + widths
            if len(output_spec['args']) == 1:
                widths = [output_spec['args'][0]['ncells'], output_spec['entries'][0]['ncells']]

            # print first line
            col = 0
            if delta == 1:
                pos = 0
                for width in widths:
                    self.process_header_supercell(0, col, table.super_grid[0][pos])
                    pos += width
                    col += 1
                self.end_line()

            for row in range(delta, self.table.R):
                pos = 0
                col = 0
                for width in widths:
                    if delta == 1 and pos == 0 and len(output_spec['args']) == 2:
                        self.process_cell(row, col, table.grid[row][pos])
                        pos += width
                        col += 1
                        continue
                    self.process_supercell(row, col, table.super_grid[row][pos])
                    pos += width
                    col += 1
                self.end_line()
        self.end_table()


class CSVExporter(Exporter):
    """
    A class to export a talbe as a CSV file
    """
    def __init__(self, table: MetaZone, exportOptions: dict):
        super().__init__(table, exportOptions)
        self.csv_writer = csv.writer(
            sys.stdout, delimiter=exportOptions['exportFormatSpec']['delimiter'],
            quotechar=exportOptions['exportFormatSpec']['quotechar'],
            quoting=csv.QUOTE_ALL
        )
        self.line: List[str] = []

    def process_cell(self, i: int, j: int, cell: MetaCell = None):
        if cell is not None:
            self.line.append(cell.val)
        else:
            self.line.append('')

    def end_line(self):
        self.csv_writer.writerow(self.line)
        self.line = []

    def process_supercell(self, i: int, j: int, supercell: SuperCell = None):
        if supercell is not None:
            number_view = supercell.get_number_view()
            if number_view is None:
                self.line.append('')
            else:
                if self.exportOptions['export-asFloat']:
                    self.line.append(str(float(number_view)))
                else:
                    self.line.append(str(number_view))
        else:
            self.line.append('')

    def process_header_supercell(self, i: int, j: int, supercell: SuperCell = None):
        if supercell is not None:
            self.line.append(supercell.zones[0][0].val)
        else:
            self.line.append('')

    def end_table(self):
        pass


class LatexExporter(Exporter):
    """
    A class to export a table in Latex format (as a .tex source file, or as a .pdf)
    """
    def __init__(self, table: MetaZone, exportOptions: dict, format: str = None):
        super().__init__(table, exportOptions)
        self.format = format
        geometry_options = {
            "margin": "2.54cm",
            "includeheadfoot": True
        }
        self.doc = Document(page_numbers=True, geometry_options=geometry_options)
        # Generate data table
        if len(self.table.spec['args']) == 1:
            if self.exportOptions['export-multipleCells']:
                str_header = ('|' +
                              (self.table.spec['args'][0]['decpos'])*'c' +
                              '|' +
                              (self.table.spec['args'][0]['ncells'] - self.table.spec['args'][0]['decpos'])*'c' +
                              '||' +
                              (self.table.spec['entries'][0]['decpos']) * 'c' +
                              '|' +
                              (self.table.spec['entries'][0]['ncells'] - self.table.spec['entries'][0]['decpos']) * 'c' +
                              '|'
                              )
                self.data_table = LongTable(str_header)
            else:
                self.data_table = LongTable('|' + 2 * 'l|')
        else:
            raise ValueError('Latex (and pdf) export not supported for 2-argument tables yet.')
        self.data_table.add_hline()
        self.line: List[str] = []
        self.nline = 0

    def process_cell(self, i: int, j: int, cell: MetaCell = None):
        if cell is not None:
            if self.exportOptions['export-errors'] and not cell.is_info() and not self.exportOptions['export-multipleCells'] and cell.parent.test_at_least_one_prop('error', True):
                self.line.append(TextColor('red', cell.val))
            elif self.exportOptions['export-errors'] and not cell.is_info() and cell.props['error']:
                self.line.append(TextColor('red', cell.val))
            else:
                self.line.append(cell.val)
        else:
            self.line.append('')

    def process_supercell(self, i: int, j: int, supercell: SuperCell = None):
        if supercell is not None:
            number_view = supercell.get_number_view()
            if number_view is None:
                self.line.append('')
            else:
                error = False
                content = ''
                if supercell.test_at_least_one_prop('error', True) and self.exportOptions['export-errors']:
                    error = True
                if self.exportOptions['export-asFloat']:
                    content = str(float(number_view))
                else:
                    content = str(number_view)
                if error:
                    self.line.append(TextColor('red', content))
                else:
                    self.line.append(content)
        else:
            self.line.append('')

    def process_header_supercell(self, i: int, j: int, supercell: SuperCell = None):
        if supercell is not None:
            self.line.append(supercell.zones[0][0].val)
        else:
            self.line.append('')

    def end_line(self):
        for elm in self.line:
            if elm != "":
                break
        else:
            self.line = []
            return
        if self.nline == 0:
            res = []
            current = None, 0
            for elm in self.line:
                if elm != "":
                    if current[0] is not None:
                        res.append(current)
                    current = elm, 1
                else:
                    current = current[0], current[1] + 1
            res.append(current)
            row = [MultiColumn(n, align='|c|', data=str(s)) for (s, n) in res]

            self.data_table.add_row(row)
        else:
            self.data_table.add_row(self.line)
        self.data_table.add_hline()
        if self.nline == 0:
            self.data_table.add_hline()
        self.line = []
        self.nline += 1

    def end_table(self):
        self.doc.append(self.data_table)

        if self.format == 'pdf':
            filename = tempfile.mkstemp()[1].split('/')[-1]
            # filename = 'lapin'
            try:
                self.doc.generate_pdf(filename)
            except subprocess.CalledProcessError as e:
                eprint('==> Warning: compilation ended with an error:')
                eprint(traceback.format_exception(None, e, e.__traceback__), flush=True)
                eprint('==> Trying to open pdf file anyway...')

            fichier = open(filename + '.pdf', 'rb')
            sys.stdout.buffer.write(fichier.read())
            fichier.close()

            os.remove(filename + '.pdf')
            
            clean_extensions = ['.aux', '.fdb_latexmk', '.fls', '.log', '.tex']
            for extension in clean_extensions:
                if os.path.isfile(filename + extension):
                    os.remove(filename + extension)

        else:
            filename = tempfile.mkstemp()[1].split('/')[-1]
            # filename = 'lapin'
            self.doc.generate_tex(filename)

            fichier = open(filename + '.tex', 'rb')
            sys.stdout.buffer.write(fichier.read())
            fichier.close()

            os.remove(filename + '.tex')


class ODSExporter(Exporter):
    """
    A class to export a table as a .ods sheet
    """
    def __init__(self, table: MetaZone, exportOptions: dict):
        super().__init__(table, exportOptions)
        self.t = SodsSpreadSheet(self.table.R + 5, self.table.C + 5)

    def end_line(self):
        pass

    def style_coordinates(self, coordinates: Tuple[int, int],
                          output_coordinates: Tuple[int, int] = None):
        if output_coordinates is None:
            output_coordinates = coordinates
        i, j = coordinates
        cell = self.table.grid[i][j]
        if cell is not None:
            if self.exportOptions['export-errors'] and not cell.is_info():
                if cell.props['error'] or (not self.exportOptions['export-multipleCells'] and cell.parent.test_at_least_one_prop('error', True)):
                    self.t.setStyle(coordinates_to_noob_system(output_coordinates), color="#e00000")
            css_classes = self.table.css_grid[cell.cartesian_coordinates[0]][cell.cartesian_coordinates[1]]
            assert css_classes is not None
            if cell.is_entry() and 'even0' in css_classes:
                self.t.setStyle(coordinates_to_noob_system(output_coordinates), background_color="#d7d7d7")
            if cell.is_entry() and 'odd0' in css_classes:
                self.t.setStyle(coordinates_to_noob_system(output_coordinates), background_color="#d7dfd7")
            if cell.is_argument():
                self.t.setStyle(coordinates_to_noob_system(output_coordinates), background_color="#eaeaea")
            if self.exportOptions['export-multipleCells']:
                if not cell.is_info():
                    self.t.setStyle(coordinates_to_noob_system(coordinates), border_bottom="1pt solid #000000")
                    # self.t.setStyle(coordinates_to_noob_system(coordinates), column_width="1cm")
                    if cell.is_first():
                        self.t.setStyle(coordinates_to_noob_system(coordinates), border_right="2pt solid #00c000")
                    if cell.is_last():
                        self.t.setStyle(coordinates_to_noob_system(coordinates), border_right="2pt solid #000000")

    def process_cell(self, i: int, j: int, cell: MetaCell = None):
        coordinates = (i, j)
        self.t.setStyle(coordinates_to_noob_system(coordinates), format='#,##0.00' if not self.exportOptions['export-multipleCells'] else '')
        self.t.setValue(coordinates_to_noob_system(coordinates), '' if cell is None else cell.val)
        self.style_coordinates((i, j))

    def process_header_supercell(self, i: int, j: int, supercell: SuperCell = None):
        coordinates = (i, j)
        self.t.setStyle(coordinates_to_noob_system(coordinates), format='#,##0.00' if not self.exportOptions['export-multipleCells'] else '')
        self.t.setValue(coordinates_to_noob_system(coordinates), '' if supercell is None else supercell.zones[0][0].val)
        if supercell is not None:
            self.style_coordinates(supercell.zones[-1][0].cartesian_coordinates, (i, j))

    def process_supercell(self, i: int, j: int, supercell: SuperCell = None):
        coordinates = (i, j)
        self.t.setStyle(coordinates_to_noob_system(coordinates), format='#,##0.00' if not self.exportOptions['export-multipleCells'] else '')
        number_view = supercell.get_number_view() if supercell is not None else None
        if number_view is None:
            self.t.setValue(coordinates_to_noob_system(coordinates), '')
        else:
            if self.exportOptions['export-asFloat']:
                self.t.setValue(coordinates_to_noob_system(coordinates), '' if supercell is None else float(number_view))
            else:
                self.t.setValue(coordinates_to_noob_system(coordinates), '' if supercell is None else str(number_view))
        if supercell is not None:
            self.style_coordinates(supercell.zones[-1][0].cartesian_coordinates, (i, j))

    def end_table(self):
        ts = SodsOds(self.t)
        filename = tempfile.mkstemp(suffix='.ods')[1]
        ts.save(filename)

        fichier = open(filename, 'rb')
        sys.stdout.buffer.write(fichier.read())
        fichier.close()

        os.remove(filename)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Export a tableContent object to chosen format.')
    parser.add_argument('JSON', help='A Json array containg the table and the options.\n'
                                     'See source code for an exemple.')
    args = parser.parse_args()
    try:
        options = json.loads(args.JSON)
    except json.decoder.JSONDecodeError:
        eprint('The format of the argument is not correct (JSON expected)')
        sys.exit(1)

    for key in ['tableContent', 'inputSpec', 'exportFormat']:
        if key not in options:
            eprint('The format of the argument is not correct (%s expected)' % key)
            sys.exit(1)

    tableContentFileName = options['tableContent']
    fichier = open(tableContentFileName, 'r')
    tableContent = fichier.read()
    fichier.close()
    input_spec = options['inputSpec']
    output_spec = copy.deepcopy(input_spec)
    same_spec = True
    if 'outputSpec' in options:
        output_spec = options['outputSpec']
        same_spec = False

    exportFormat = options['exportFormat']

    if exportFormat == 'json':
        print(tableContent)
        sys.exit(0)

    exportOptions = {
        # 'separators': None,                     # separators in the case of mono-column values
        'export-headers': True,                          # presence of the title of the arguments
        'export-multipleCells': True,                    # values split across multiple cells or not
        'export-asFloat': False,
        'export-doubleDigits': False,                        # remove zero padding
        'export-comments': True,
        'export-diffFiles': -1,
        'export-errors': False,
        'exportFormatSpec': {}
    }

    if exportFormat == 'csv':
        exportOptions['commentary'] = False
        exportOptions['exportFormatSpec'] = {
            'delimiter': ';',
            'quotechar': '"'
        }

    if 'exportOptions' in options:
        update_dictionary(exportOptions, options['exportOptions'])

    table: Optional[MetaZone] = None

    if len(input_spec['args']) == 1:
        table = Table1ArgZone_Main(output_spec, info={'headers': exportOptions['export-headers']})

    if len(input_spec['args']) == 2:
        table = Table2ArgZone_Main(output_spec, info={'headers': exportOptions['export-headers']})

    if table is None:
        eprint('Could not find a proper layout for the table')
        sys.exit(1)

    if same_spec:
        table.from_original_JSON(tableContent)
    else:
        table.from_original_JSON(tableContent, input_spec)

    if not exportOptions['export-doubleDigits']:
        for i in range(table.R):
            for j in range(table.C):
                cell = table.grid[i][j]
                if cell is not None:
                    if len(cell.val) >= 2 and cell.val[0] == '0':
                        cell.val = cell.val[1]

    exporter: Exporter

    if exportFormat == 'csv':
        exporter = CSVExporter(table, exportOptions)
        exporter.process_table()

    if exportFormat == 'ods':
        exporter = ODSExporter(table, exportOptions)
        exporter.process_table()

    if exportFormat == 'pdf' or exportFormat == 'latex':
        exporter = LatexExporter(table, exportOptions, format=exportFormat)
        exporter.process_table()

if __name__ == "__example__":
    # below is an example of input JSON for the script
    optionExample = {
        "exportFormat": "csv",
        "exportOptions": {
            "export-headers": True,
            "export-diffFiles": -1,
            "export-multipleCells": True,
            "export-asFloat": False,
            "export-doubleDigits": False,
            "export-errors": False,
            "export-comments": False,
            "export-difference": False,
            "template-export-option": False,
            "cell-export-option": False,
            "export-asString": False,
            "difference-export-option": False,
            "export-main-table": True
        },
        "tableContent": "/path_to_project/web/pythonScript/table_content_vGIHtj",
        "inputSpec": {
            "table_type": "6",
            "readonly": False,
            "args": [
                {
                    "name": "angle",
                    "type": "integer_and_sexagesimal",
                    "nsteps": 90,
                    "ncells": 2,
                    "decpos": 1
                }
            ],
            "entries": [
                {
                    "name": "Entry",
                    "type": "sexagesimal",
                    "ncells": 4,
                    "decpos": 1
                },
                {
                    "name": "Difference",
                    "type": "sexagesimal",
                    "ncells": 4,
                    "decpos": 1
                }
            ]
        },
        "filename": "trigonometrical__sine.csv"
    }

    # below is an example of typical JSON content for the attribute "tableContent"
    tableExample = {
        "args": {
            "argument1": [
                {
                    "value": ["1"],
                    "comment": "",
                    "suggested": False,
                    "critical_apparatus": [""]
                },
                ...
            ]
        },
        "entry": [
            {
                "value": ["30", "18"],
                "comment": "",
                "suggested": False,
                "critical_apparatus": ["", ""]
            },
            ...
        ],
        "edition_tables": {
            "baseEdition": "12",
            "option": "by-base",
            "tables": {
                "26": {
                    "editedTextId": 12,
                    "editedTextTitle": "Lunar Velocity_Tabule Magne_Paris Ms",
                    "tableId": "26",
                    "weight": "",
                    "siglum": "A"
                },
                "30": {
                    "editedTextId": 16,
                    "editedTextTitle": "Lunar Velocity_Tabule Magne_Vatican Ms1",
                    "tableId": "30",
                    "weight": "",
                    "siglum": "B"
                },
                "34": {
                    "editedTextId": 20,
                    "editedTextTitle": "Lunar Velocity_Tabule Magne_Vatican Ms2",
                    "tableId": "34",
                    "weight": "",
                    "siglum": "C"
                }
            },
            "activated": True
        },
        "symmetries": []
    }
