DROP TABLE IF EXISTS "pages";
CREATE TABLE "pages" (
    "idx" INTEGER PRIMARY KEY  NOT NULL,
    "pageId" VARCHAR NOT NULL DEFAULT "foo" ,
    "authorIdx" INTEGER NOT NULL DEFAULT (0),
    "title" VARCHAR NOT NULL DEFAULT "Unnamed page",
    "content" TEXT NOT NULL DEFAULT "",
    "parentIdx" INTEGER NOT NULL DEFAULT (0)
);

CREATE UNIQUE INDEX "main"."pages_pageId" ON "pages" ("pageId" ASC);

INSERT INTO "pages" VALUES(1,'escape',1,'escape','The escape filter converts the characters &, <, >, '', and " in strings to HTML-safe sequences. Use this if you need to display text that might contain such characters in HTML:

    {{ user->username|escape }}
For convenience, the e filter is defined as an alias:

    {{ user->username|e }}
The escape filter can also be used in another context than HTML; for instance, to escape variables included in a JavaScript:

    {{ user->username|escape(''js'') }}
    {{ user->username|e(''js'') }}
Internally, escape uses the PHP native htmlspecialchars function.',0);
INSERT INTO "pages" VALUES(2,'join',1,'join','The join filter returns a string which is the concatenation of the items of a sequence:

    {{ array(1, 2, 3) | join }}
    {# returns 123 #}
The separator between elements is an empty string per default, but you can define it with the optional first parameter:

    {{ array(1, 2, 3) | join(''|'') }}
    {# returns 1|2|3 #}',0);
INSERT INTO "pages" VALUES(3,'forms',1,'Forms','The easiest way to render a form in a view is just call the form directly. It creates a default html markup including label and messaging elements if needed.
    {{ form }}

The markup can also we modified via an filter. The filter itself can iterate over the elements an apply the right markup for each input field. The input field has the method input->markup() to get the pure markup for that element.
    {{ form | myFormDecorator }}

The following blocks just shows the YATE syntax highlighter:
    <div id="foo">{{ form }}</div>

    <!DOCTYPE html>
    <html>
        <head>
            <title>My Webpage</title>
        </head>
        <body>
            <ul id="navigation-{{ id | md5 }}">
            {{ for item in navigation }}
                <li>{{ item->caption | link(item->href) }}</li>
            {{ end }}
            </ul>

            <h1>{{ pageTitle | capitalize }}</h1>
            <div>{{ data | join(", ")}}</div>
        </body>
    </html>',0);
