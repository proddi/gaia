/*
    switch (pattern[2]) {
    case -1:
      // do nothing
      break;
    case -2:
      // exit
      patternStack.pop();
      break;
    case -3:
      // exitall
      patternStack.length = 0;
      break;
    default:
      // this was the start of a delimited pattern or a state/environment
      patternStack.push(pattern);
      break;
    }
 */

if (! this.sh_languages) {
  this.sh_languages = {};
}
sh_languages['qml'] = [
  [ // 0
    [
      /\b(?:import|include_once|require|require_once)\b/g,
      'sh_preproc',
      -1
    ],
    [
      /\/\//g,
      'sh_comment',
      1
    ],
    [
      /"/g,
      'sh_string',
      2
    ],
    [
      /'/g,
      'sh_string',
      3
    ],
    [
      /([A-Z][A-Za-z0-9_]*)/g,
//      /([A-Z][A-Za-z0-9]*)/g,
      'sh_classname',
      4
    ]
  ],
  [ // 1 - until end of line
    [
      /$/g,
      null,
      -2
    ]
  ],
  [ // 2 - double quoted strings
    [
      /\\(?:\\|")/g,
      null,
      -1
    ],
    [
      /"/g,
      'sh_string',
      -2
    ]
  ],
  [ // 3 - single quoted strings
    [
      /\\(?:\\|')/g,
      null,
      -1
    ],
    [
      /'/g,
      'sh_string',
      -2
    ]
  ],
  [ // 4 - inside a QML object
    [
      /[\{|\}]/g,
      'sh_cbracket',
      -1
    ],
    [
      /\/\//g,
      'sh_comment',
      1
    ],
    [
      /\b[+-]?(?:(?:0x[A-Fa-f0-9]+)|(?:(?:[\d]*\.)?[\d]+(?:[eE][+-]?[\d]+)?))u?(?:(?:int(?:8|16|32|64))|L)?\b/g,
      'sh_number',
      -1
    ],
    [
      /"/g,
      'sh_string',
      2
    ],
    [
      /'/g,
      'sh_string',
      3
    ],
    [
      /([A-Z][A-Za-z0-9_]*)/g,
//      /([A-Z][A-Za-z0-9]*)/g,
      'sh_classname',
      4
    ],
    [
      /([a-z][A-Za-z0-9_\.]*)(\:)/g,
//      /([A-Z][A-Za-z0-9]*)/g,
      ['sh_keyword', 'sh_symbol'],
      -1
    ],
    [ // id
      /d([A-Za-z0-9_]*)$/g,
//      /([A-Z][A-Za-z0-9]*)/g,
      'sh_type',
      -1
    ]
  ]
];
