with open('lib/screens/pt_list_screen.dart', 'r') as f:
    c = f.read()
    
# Remove the extra `      ),` and `    );` 
c = c.replace("      },\n      ),\n    );\n  }\n}", "      },\n    );\n  }\n}")
c = c.replace("      },\n    );\n  }\n}", "      },\n    );\n  }\n}")
# Let's be safer and just replace the exact end of _buildPtList
# Actually, wait. RefreshIndicator wraps ListView.builder.
# RefreshIndicator( child: ListView.builder( ... itembuilder: (){} ) )
# So we need `      },\n    ),\n  );`
import re
c = re.sub(r'      \},\n      \),\n    \);\n  \}\n\}', r'      },\n    ),\n    );\n  }\n}', c)

with open('lib/screens/pt_list_screen.dart', 'w') as f:
    f.write(c)
