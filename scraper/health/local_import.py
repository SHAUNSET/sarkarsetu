import json
import mysql.connector
import os

DB_CONFIG = {'host': 'localhost', 'user': 'root', 'password': '', 'database': 'sarkarsetu'}

def import_health():
    # Locate the data file in the same folder as this script
    script_dir = os.path.dirname(os.path.abspath(__file__))
    file_path = os.path.join(script_dir, "data_dump.json")

    # Load the JSON data
    with open(file_path, "r", encoding="utf-8") as f:
        data = json.load(f)

    # Navigate the JSON structure correctly
    items = data.get("data", {}).get("hits", {}).get("items", [])
    
    db = mysql.connector.connect(**DB_CONFIG)
    cursor = db.cursor()

    # INSERT IGNORE ensures that if a title already exists, it skips it
    query = """
        INSERT IGNORE INTO schemes (title, description, category, eligibility, ministry) 
        VALUES (%s, %s, %s, %s, %s)
    """
    
    print(f"📦 Importing {len(items)} health schemes from data_dump.json...")
    count = 0
    
    for item in items:
        f = item.get("fields", {})
        
        # Extract categories as a comma-separated string
        raw_cats = f.get("schemeCategory", ["General"])
        category_string = ", ".join(raw_cats) if isinstance(raw_cats, list) else raw_cats
        
        batch = (
            f.get("schemeName", "Untitled"),
            f.get("briefDescription", "No description."),
            category_string, 
            f.get("eligibilityCriteria", "Check official portal."),
            f"{f.get('ministryName', 'Central')}"
        )
        
        cursor.execute(query, batch)
        count += cursor.rowcount 
    
    db.commit()
    cursor.close()
    db.close()
    
    print(f"✅ Import complete. Successfully inserted {count} schemes into 'schemes' table.")

if __name__ == "__main__":
    import_health()
