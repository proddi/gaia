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

if (! this.sh_languages) {
  this.sh_languages = {};
}
sh_languages['yate'] = [
  [ // 0
    [
      /<\?xml/g,
      'sh_preproc',
      1,
      1
    ],
    [
      /<!DOCTYPE/g,
      'sh_preproc',
      3,
      1
    ],
    [
      /<!--/g,
      'sh_comment',
      4
    ],
    [
      /<(?:\/)?[A-Za-z](?:[A-Za-z0-9_:.-]*)(?:\/)?>/g,
      'sh_keyword',
      -1
    ],
    [
      /<(?:\/)?[A-Za-z](?:[A-Za-z0-9_:.-]*)/g,
      'sh_keyword',
      5,
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
      5,
      1
    ],
    [
        /{# /g,
        'sh_comment',
        6
    ],
    [
        /{{ /g,
        'sh_yate_code',
        7
    ]
  ],
  [ // 1
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
      2
    ]
  ],
  [ // 2
    [
        /{{ /g,
        'sh_yate_code',
        7
    ],
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
  [ // 3
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
      2
    ]
  ],
  [ // 4
    [
      /-->/g,
      'sh_comment',
      -2
    ],
    [
      /<!--/g,
      'sh_comment',
      4
    ]
  ],
  [ // 5
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
      2
    ]
  ],
  [ // #6 inside a {{# block
    [
        /#}/g,
        null,
        -2
    ],

  ],
  [ // #7 inside a {{\S? block

    // is it a keyword?
    [
        /\b(?:for|in|if|end|else|do)\b/gi,
        'sh_yate_keyword',
        -1
    ],

    [
      /'/g,
      'sh_yate_string',
      8
    ],

    [
      /"/g,
      'sh_yate_string',
      9
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
  [ // #8 inside a ' string
    [
      /'/g,
      'sh_yate_string',
      -2
    ],

  ],
  [ // #9 inside a " string
    [
      /"/g,
      'sh_yate_string',
      -2
    ],

  ]
];
















sh_languages['yate_old'] = [
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
