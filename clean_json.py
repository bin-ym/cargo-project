import json
import re
import os

def clean_json_file(filepath):
    print(f"Cleaning {filepath}...")
    if not os.path.exists(filepath):
        print(f"File {filepath} not found.")
        return

    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        all_translations = {}
        
        # Extract all "key": "value" patterns
        # This regex handles escaped quotes in values
        pattern = re.compile(r'"([^"]+)"\s*:\s*"((?:[^"\\]|\\.)*)"')
        matches = pattern.findall(content)
        
        for key, value in matches:
            all_translations[key] = value
            
        if not all_translations:
            print(f"No translations found in {filepath}")
            return

        with open(filepath, 'w', encoding='utf-8') as f:
            json.dump(all_translations, f, ensure_ascii=False, indent=2)
        print(f"Successfully cleaned {filepath}")
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

if __name__ == "__main__":
    clean_json_file('backend/languages/en.json')
    clean_json_file('backend/languages/am.json')
