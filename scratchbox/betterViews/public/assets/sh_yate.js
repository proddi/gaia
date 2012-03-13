if (! this.sh_languages) {
  this.sh_languages = {};
}
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
sh_languages['yate'] = [
  [
    [
        /{# /g,
        'sh_comment',
        1
    ],
    [
        /{{ /g,
        'sh_yate_code',
        2
    ]
/*
    [
      /(\b[A-Za-z0-9_]+:)((?:[^,=]*$)?)/g,
      ['sh_keyword', 'sh_string'],
      -1
    ],
    [
      /([A-Za-z0-9_]+)(=)([^,]+)(,?)/g,
      ['sh_attribute', 'sh_symbol', 'sh_string', 'sh_symbol'],
      -1
    ]*/
  ],
  [ // #1 inside a {{# block
    [
        /#}/g,
        null,
        -2
    ],

  ],
  [ // #2 inside a {{\S? block

    // is it a keyword?
    [
        /\b(?:for|in|if|end|else|do)\b/gi,
        'sh_keyword',
        -1
    ],

    [
      /'/g,
      'sh_string',
      3
    ],

    [
      /"/g,
      'sh_string',
      4
    ],

    // number ?
    [
      /\b[+-]?(?:(?:0x[A-Fa-f0-9]+)|(?:(?:[\d]*\.)?[\d]+(?:[eE][+-]?[\d]+)?))u?(?:(?:int(?:8|16|32|64))|L)?\b/g,
      'sh_number',
      -1
    ],

    // identifier ?
    [
        /\w+/g,
        'sh_identifier',
        -1
    ],

    // symbol ?
    [
        /\||\(|\)|\[|\]/g,
        'sh_symbol',
        -1
    ],

    // end of yate ?
    [
        /[%}]}/g,
        null,
        -2
    ],

  ],
  [ // #3 inside a ' string
    [
      /'/g,
      'sh_string',
      -2
    ],

  ],
  [ // #3 inside a " string
    [
      /"/g,
      'sh_string',
      -2
    ],

  ]
];

sh_languages['yate2'] = [
  [
//    [
//      /\b(?:VARCHAR|TINYINT|TEXT|DATE|SMALLINT|MEDIUMINT|INT|BIGINT|FLOAT|DOUBLE|DECIMAL|DATETIME|TIMESTAMP|TIME|YEAR|UNSIGNED|CHAR|TINYBLOB|TINYTEXT|BLOB|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT|ENUM|BOOL|BINARY|VARBINARY)\b/gi,
//      'sh_type',
//      -1
//    ],
//    [
//      /\b  (?:for|in|if|end)\b/gi,
//      'sh_keyword',
//      -1
//    ],
    [
      /\b(?:for|in|if|end)\b/gi,
      'sh_keyword',
      -1
    ],
    [
      /"/g,
      'sh_string',
      -1
    ],
    [
      /'/g,
      'sh_string',
      2
    ],
//    [
//      /`/g,
//      'sh_string',
//      3
//    ],
    [
      /{{#/g,
      'sh_comment',
      4
    ],
    [
      /{{\S? /g,
      'sh_yate_begin',
      -2
    ],
    [
      /}}/g,
      'sh_yate_end',
      -2
    ],
    [
      /\/\*\*/g,
      'sh_comment',
      11
    ],
    [
      /\/\*/g,
      'sh_comment',
      12
    ],
    [
      /--/g,
      'sh_comment',
      4
    ],
//    [
//      /~|!|%|\^|\*|\(|\)|-|\+|=|\[|\]|\\|:|;|,|\.|\/|\?|&|<|>|\|/g,
//      'sh_symbol',
//      -1
//    ],
    [
      /\||\(|\)/g,
      'sh_symbol',
      -1
    ],
    [
      /\b[+-]?(?:(?:0x[A-Fa-f0-9]+)|(?:(?:[\d]*\.)?[\d]+(?:[eE][+-]?[\d]+)?))u?(?:(?:int(?:8|16|32|64))|L)?\b/g,
      'sh_number',
      -1
    ],
//    [
//        /(join)\(/g,
//        'sh_function',
//        -1
//    ],
    [
      /'/g,
      'sh_string',
      2
    ]
  ],
  [
//    [
//      /"/g,
//      'sh_string',
//      -2
//    ],
    [
      /\\./g,
      'sh_specialchar',
      -1
    ]
  ],
  [
    [
      /'/g,
      'sh_string',
      -2
    ],
    [
      /\\./g,
      'sh_specialchar',
      -1
    ]
  ],
  [
    [
      /`/g,
      'sh_string',
      -2
    ],
    [
      /\\./g,
      'sh_specialchar',
      -1
    ]
  ],
  [
    [
      /$/g,
      null,
      -2
    ]
  ],
  [
    [
      /$/g,
      null,
      -2
    ],
    [
      /(?:<?)[A-Za-z0-9_\.\/\-_~]+@[A-Za-z0-9_\.\/\-_~]+(?:>?)|(?:<?)[A-Za-z0-9_]+:\/\/[A-Za-z0-9_\.\/\-_~]+(?:>?)/g,
      'sh_url',
      -1
    ],
    [
      /<\?xml/g,
      'sh_preproc',
      6,
      1
    ],
    [
      /<!DOCTYPE/g,
      'sh_preproc',
      8,
      1
    ],
    [
      /<!--/g,
      'sh_comment',
      9
    ],
    [
      /<(?:\/)?[A-Za-z](?:[A-Za-z0-9_:.-]*)(?:\/)?>/g,
      'sh_keyword',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z](?:[A-Za-z0-9_:.-]*)/g,
      'sh_keyword',
      10,
      1
    ],
    [
      /&(?:[A-Za-z0-9]+);/g,
      'sh_preproc',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z][A-Za-z0-9]*(?:\/)?>/g,
      'sh_keyword',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z][A-Za-z0-9]*/g,
      'sh_keyword',
      10,
      1
    ],
    [
      /@[A-Za-z]+/g,
      'sh_type',
      -1
    ],
    [
      /(?:TODO|FIXME|BUG)(?:[:]?)/g,
      'sh_todo',
      -1
    ]
  ],
  [
    [
      /\?>/g,
      'sh_preproc',
      -2
    ],
    [
      /([^=" \t>]+)([ \t]*)(=?)/g,
      ['sh_type', 'sh_normal', 'sh_symbol'],
      -1
    ],
    [
      /"/g,
      'sh_string',
      7
    ]
  ],
  [
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
  [
    [
      />/g,
      'sh_preproc',
      -2
    ],
    [
      /([^=" \t>]+)([ \t]*)(=?)/g,
      ['sh_type', 'sh_normal', 'sh_symbol'],
      -1
    ],
    [
      /"/g,
      'sh_string',
      7
    ]
  ],
  [
    [
      /-->/g,
      'sh_comment',
      -2
    ],
    [
      /<!--/g,
      'sh_comment',
      9
    ]
  ],
  [
    [
      /(?:\/)?>/g,
      'sh_keyword',
      -2
    ],
    [
      /([^=" \t>]+)([ \t]*)(=?)/g,
      ['sh_type', 'sh_normal', 'sh_symbol'],
      -1
    ],
    [
      /"/g,
      'sh_string',
      7
    ]
  ],
  [
    [
      /\*\//g,
      'sh_comment',
      -2
    ],
    [
      /(?:<?)[A-Za-z0-9_\.\/\-_~]+@[A-Za-z0-9_\.\/\-_~]+(?:>?)|(?:<?)[A-Za-z0-9_]+:\/\/[A-Za-z0-9_\.\/\-_~]+(?:>?)/g,
      'sh_url',
      -1
    ],
    [
      /<\?xml/g,
      'sh_preproc',
      6,
      1
    ],
    [
      /<!DOCTYPE/g,
      'sh_preproc',
      8,
      1
    ],
    [
      /<!--/g,
      'sh_comment',
      9
    ],
    [
      /<(?:\/)?[A-Za-z](?:[A-Za-z0-9_:.-]*)(?:\/)?>/g,
      'sh_keyword',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z](?:[A-Za-z0-9_:.-]*)/g,
      'sh_keyword',
      10,
      1
    ],
    [
      /&(?:[A-Za-z0-9]+);/g,
      'sh_preproc',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z][A-Za-z0-9]*(?:\/)?>/g,
      'sh_keyword',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z][A-Za-z0-9]*/g,
      'sh_keyword',
      10,
      1
    ],
    [
      /@[A-Za-z]+/g,
      'sh_type',
      -1
    ],
    [
      /(?:TODO|FIXME|BUG)(?:[:]?)/g,
      'sh_todo',
      -1
    ]
  ],
  [
    [
      /\*\//g,
      'sh_comment',
      -2
    ],
    [
      /(?:<?)[A-Za-z0-9_\.\/\-_~]+@[A-Za-z0-9_\.\/\-_~]+(?:>?)|(?:<?)[A-Za-z0-9_]+:\/\/[A-Za-z0-9_\.\/\-_~]+(?:>?)/g,
      'sh_url',
      -1
    ],
    [
      /(?:TODO|FIXME|BUG)(?:[:]?)/g,
      'sh_todo',
      -1
    ]
  ]
];