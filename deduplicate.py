import json
from collections import OrderedDict
import sys

def deduplicate_json(filepath):
    print(f"Processing {filepath}...")
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            lines = f.readlines()
        
        # Filter out markdown code blocks and empty lines
        cleaned_lines = []
        for line in lines:
            s = line.strip()
            if s in ('```', 'json', '```json', '```javascript'):
                continue
            cleaned_lines.append(line)
        
        content = ''.join(cleaned_lines)
        
        # Try to find the JSON object if there's extra text
        start = content.find('{')
        end = content.rfind('}')
        if start != -1 and end != -1:
            content = content[start:end+1]
        
        data = json.loads(content, object_pairs_hook=OrderedDict)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        print(f"Successfully deduplicated {filepath}")
    except Exception as e:
        print(f"Error deduplicating {filepath}: {e}")
        # Print a bit of the content to see what's wrong
        print("Content snippet around error:")
        print(content[:100] + "..." + content[-100:])

deduplicate_json('c:/xampp/htdocs/cargo-project/backend/languages/en.json')
deduplicate_json('c:/xampp/htdocs/cargo-project/backend/languages/am.json')
