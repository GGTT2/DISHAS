/**
 * When search a table with accented characters, it can be frustrating to have
 * an input such as _Zurich_ not match _Zürich_ in the table (`u !== ü`). This
 * type based search plug-in replaces the built-in string formatter in
 * DataTables with a function that will remove replace the accented characters
 * with their unaccented counterparts for fast and easy filtering.
 *
 * Note that with the accented characters being replaced, a search input using
 * accented characters will no longer match. The second example below shows
 * how the function can be used to remove accents from the search input as well,
 * to mitigate this problem.
 *
 *  @summary Replace accented characters with unaccented counterparts
 *  @name Accent neutralise
 *  @author Allan Jardine [The list of diacritic accent from the website http://www.vingtseptpointsept.fr/2011/09/22/une-fonction-javascript-pour-eliminer-accents-et-diacritiques/]
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable();
 *    } );
 *
 *  @example
 *    
 */

(function () {

    function removeAccents(data) {
        return data
//                .replace(/έ/g, 'ε')
//                .replace(/[ύϋΰ]/g, 'υ')
//                .replace(/ό/g, 'ο')
//                .replace(/ώ/g, 'ω')
//                .replace(/ά/g, 'α')
//                .replace(/[ίϊΐ]/g, 'ι')
//                .replace(/ή/g, 'η')
//                .replace(/\n/g, ' ')
//                .replace(/á/g, 'a')
//                .replace(/é/g, 'e')
//                .replace(/í/g, 'i')
//                .replace(/ó/g, 'o')
//                .replace(/ú/g, 'u')
//                .replace(/ê/g, 'e')
//                .replace(/î/g, 'i')
//                .replace(/ô/g, 'o')
//                .replace(/è/g, 'e')
//                .replace(/ï/g, 'i')
//                .replace(/ü/g, 'u')
//                .replace(/ã/g, 'a')
//                .replace(/õ/g, 'o')
//                .replace(/ç/g, 'c')
//                .replace(/ì/g, 'i')
//______________________________ http://www.vingtseptpointsept.fr/2011/09/22/une-fonction-javascript-pour-eliminer-accents-et-diacritiques/
                .replace(/[\u00AA\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g, 'a')
                .replace(/[\u00C0\u00C1\u00C2\u00C3\u00C4\u00C5]/g, 'A')
                .replace(/[\u00E6]/g, 'ae')
                .replace(/[\u00C6]/g, 'Ae')
                .replace(/[\u00E7]/g, 'c')
                .replace(/[\u00C7]/g, 'C')
                .replace(/[\u00F0]/g, 'd')
                .replace(/[\u00D0]/g, 'D')
                .replace(/[\u00E8\u00E9\u00EA\u00EB]/g, 'e')
                .replace(/[\u00C8\u00C9\u00CA\u00CB\u1EBA]/g, 'E')
                .replace(/[\u00EC\u00ED\u00EE\u00EF]/g, 'i')
                .replace(/[\u00CC\u00CD\u00CE\u00CF]/g, 'I')
                .replace(/[\u00F1]/g, 'n')
                .replace(/[\u00D1\u1E44]/g, 'N')
                .replace(/[\u00BA\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8]/g, 'o')
                .replace(/[\u00D2\u00D3\u00D4\u00D5\u00D6\u00D8]/g, 'O')
                .replace(/[\u0152]/g, 'Oe')
                .replace(/[\u0153]/g, 'oe')
                .replace(/[\u00DF]/g, 'ss')
                .replace(/[\u1E6C]/g, 'T')
                .replace(/[\u00DE]/g, 'Th')
                .replace(/[\u00FE]/g, 'th')
                .replace(/[\u00F9\u00FA\u00FB\u00FC\u1EE7]/g, 'u')
                .replace(/[\u00D9\u00DA\u00DB\u00DC]/g, 'U')
                .replace(/[\u1E84]/g, 'W')
                .replace(/[\u1E8C]/g, 'X')
                .replace(/[\u00FD\u00FF]/g, 'y')
                .replace(/[\u00DD\u0178]/, 'Y')
                .replace(/[\u0100\u0102\u0104\u01CD\u01FA\u0200\u0202\u023A\u1E00\u1EA0\u1EA2\u1EA4\u1EA6\u1EA8\u1EAA\u1EAC\u1EAE\u1EB0\u1EB2\u1EB4\u1EB6]/g, 'A')
                .replace(/[\u0101\u0103\u0105\u01CE\u01FB\u0201\u0203\u1E01\u1E9A\u1EA1\u1EA3\u1EA5\u1EA7\u1EA9\u1EAB\u1EAD\u1EAF\u1EB1\u1EB3\u1EB5\u1EB7]/g, 'a')
                .replace(/[\u01FC]/g, 'Ae')
                .replace(/[\u01FD]/g, 'ae')
                .replace(/[\u1E02\u1E04\u1E06]/g, 'B')
                .replace(/[\u1E03\u1E05\u1E07]/g, 'b')
                .replace(/[\u0106\u0108\u010A\u010C\u023B\u1E08]/g, 'C')
                .replace(/[\u0107\u0109\u010B\u010D\u023C\u1E09]/g, 'c')
                .replace(/[\u010E\u0110\u1E0A\u1E0C\u1E0E\u1E10\u1E12]/g, 'D')
                .replace(/[\u010F\u0111\u1E0B\u1E0D\u1E0F\u1E11\u1E13]/g, 'd')
                .replace(/[\u01C4\u01F1]/g, 'DZ')
                .replace(/[\u01C5\u01F2]/g, 'DZ')
                .replace(/[\u01C6\u01F3]/g, 'dz')
                .replace(/[\u0112\u0114\u0116\u0118\u011A\u0204\u0206\u0228\u1E14\u1E16\u1E18\u1E1A\u1E1C\u1EB8\u1EBA\u1EBC\u1EBE\u1EC0\u1EC2\u1EC4\u1EC6]/g, 'E')
                .replace(/[\u0113\u0115\u0117\u0119\u011B\u0205\u0207\u0229\u1E15\u1E17\u1E19\u1E1B\u1E1D\u1EB9\u1EBB\u1EBD\u1EBF\u1EC1\u1EC3\u1EC5\u1EC7]/g, 'e')
                .replace(/[\u0192\u1E1F]/g, 'f')
                .replace(/[\u1E1E]/g, 'F')
                .replace(/[\u011C\u011E\u0120\u0122\u01F4\u1E20]/g, 'G')
                .replace(/[\u011D\u011F\u0121\u0123\u01F5\u1E21]/g, 'g')
                .replace(/[\u0124\u0126\u1E22\u1E24\u1E26\u1E28\u1E2A]/g, 'H')
                .replace(/[\u0125\u0127\u1E23\u1E25\u1E27\u1E29\u1E2B\u1E96]/g, 'h')
                .replace(/[\u0128\u012A\u012C\u012E\u0130\u01CF\u0208\u020A\u1E2C\u1E2E\u1EC8\u1ECA]/g, 'I')
                .replace(/[\u0129\u012B\u012D\u012F\u0131\u01D0\u0209\u020B\u1E2D\u1E2F\u1EC9\u1ECB]/g, 'i')
                .replace(/[\u0132]/g, 'IJ')
                .replace(/[\u0133]/g, 'ij')
                .replace(/[\u0134]/g, 'J')
                .replace(/[\u0135]/g, 'j')
                .replace(/[\u0136\u1E30\u1E32\u1E34]/g, 'K')
                .replace(/[\u0137\u1E31\u1E33\u1E35]/g, 'k')
                .replace(/[\u0139\u013B\u013D\u013F\u0141\u023D\u1E36\u1E38\u1E3A\u1E3C]/g, 'L')
                .replace(/[\u013A\u013C\u013E\u0140\u0142\u1E37\u1E39\u1E3B\u1E3D]/g, 'l')
                .replace(/[\u01C7]/g, 'LJ')
                .replace(/[\u01C8]/g, 'Lj')
                .replace(/[\u01C9]/g, 'lj')
                .replace(/[\u1E3E\u1E40\u1E42]/g, 'M')
                .replace(/[\u1E3F\u1E41\u1E43]/g, 'm')
                .replace(/[\u0143\u0145\u0147\u01F8\u1E44\u1E46\u1E48\u1E4A]/g, 'N')
                .replace(/[\u0144\u0146\u0148\u01F9\u1E45\u1E47\u1E49\u1E4B]/g, 'n')
                .replace(/[\u01CA]/g, 'NJ')
                .replace(/[\u01CB]/g, 'Nj')
                .replace(/[\u01CC]/g, 'nj')
                .replace(/[\u014C\u014E\u0150\u01A0\u01D1\u01FE\u020C\u020E\u022A\u022C\u022E\u0230\u1E4C\u1E4E\u1E50\u1E52\u1ECC\u1ECE\u1ED0\u1ED2\u1ED4\u1ED6\u1ED8\u1EDA\u1EDC\u1EDE\u1EE0\u1EE2]/g, 'O')
                .replace(/[\u014D\u014F\u0151\u01A1\u01D2\u01FF\u020D\u020F\u022B\u022D\u022F\u0231\u1E4D\u1E4F\u1E51\u1E53\u1ECD\u1ECF\u1ED1\u1ED3\u1ED5\u1ED7\u1ED9\u1EDB\u1EDD\u1EDF\u1EE1\u1EE3]/g, 'o')
                .replace(/[\u1E54\u1E56]/g, 'P')
                .replace(/[\u1E55\u1E57]/g, 'p')
                .replace(/[\u0154\u0156\u0158\u0210\u0212\u1E58\u1E5A\u1E5C\u1E5E]/g, 'R')
                .replace(/[\u0155\u0157\u0159\u0211\u0213\u1E59\u1E5B\u1E5D\u1E5F]/g, 'r')
                .replace(/[\u015A\u015C\u015E\u0160\u0218\u1E60\u1E62\u1E64\u1E66\u1E68]/g, 'S')
                .replace(/[\u015B\u015D\u015F\u0161\u017F\u0219\u023F\u1E61\u1E63\u1E65\u1E67\u1E69\u1E9B]/g, 's')
                .replace(/[\u1E9E]/g, 'SS')
                .replace(/[\u0162\u0164\u0166\u021A\u023E\u1E6A\u1E6C\u1E6E\u1E70]/g, 'T')
                .replace(/[\u0163\u0165\u0167\u021B\u1E6B\u1E6D\u1E6F\u1E71\u1E97]/g, 't')
                .replace(/[\u0168\u016A\u016C\u016E\u0170\u0172\u01AF\u01D3\u01D5\u01D7\u01D9\u01DB\u0214\u0216\u1E72\u1E74\u1E76\u1E78\u1E7A\u1EE4\u1EE6\u1EE8\u1EEA\u1EEC\u1EEE\u1EF0]/g, 'U')
                .replace(/[\u0169\u016B\u016D\u016F\u0171\u0173\u01B0\u01D4\u01D6\u01D8\u01DA\u01DC\u0215\u0217\u1E73\u1E75\u1E77\u1E79\u1E7B\u1EE5\u1EE7\u1EE9\u1EEB\u1EED\u1EEF\u1EF1]/g, 'u')
                .replace(/[\u1E7C\u1E7E]/g, 'V')
                .replace(/[\u1E7D\u1E7F]/g, 'v')
                .replace(/[\u0174\u1E80\u1E82\u1E84\u1E86\u1E88]/g, 'W')
                .replace(/[\u0175\u1E81\u1E83\u1E85\u1E87\u1E89\u1E98]/g, 'w')
                .replace(/[\u1E8A\u1E8C]/g, 'X')
                .replace(/[\u1E8B\u1E8D]/g, 'x')
                .replace(/[\u0176\u0232\u1E8E\u1EF2\u1EF4\u1EF6\u1EF8]/g, 'Y')
                .replace(/[\u0177\u0233\u1E8F\u1E99\u1EF3\u1EF5\u1EF7\u1EF9]/g, 'y')
                .replace(/[\u0179\u017B\u017D\u1E90\u1E92\u1E94]/g, 'Z')
                .replace(/[\u017A\u017C\u017E\u0240\u1E91\u1E93\u1E95]/g, 'z');
    }

    var searchType = jQuery.fn.DataTable.ext.type.search;
    searchType.string = function (data) {
        return !data ?
                '' :
                typeof data === 'string' ?
                removeAccents(data) :
                data;
    };
    searchType.html = function (data) {
        return !data ?
                '' :
                typeof data === 'string' ?
                removeAccents(data.replace(/<.*?>/g, '')) :
                data;
    };

}());