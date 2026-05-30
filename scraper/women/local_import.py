import json
import mysql.connector
import os

DB_CONFIG = {'host': 'localhost', 'user': 'root', 'password': 'Suchet1234567', 'database': 'sarkarsetu'}

def import_women():
    script_dir = os.path.dirname(os.path.abspath(__file__))
    file_path = os.path.join(script_dir, "data_dump.json")

    with open(file_path, "r", encoding="utf-8") as f:
        data = json.load(f)

    items = data.get("data", {}).get("hits", {}).get("items", [])
    db = mysql.connector.connect(**DB_CONFIG)
    cursor = db.cursor()

    # INSERT IGNORE handles potential duplicate titles automatically
    query = """
        INSERT IGNORE INTO schemes (title, description, category, eligibility, ministry) 
        VALUES (%s, %s, %s, %s, %s)
    """
    
    print(f"📦 Importing {len(items)} women schemes...")
    count = 0
    for item in items:
        f = item.get("fields", {})
        raw_cats = f.get("schemeCategory", ["Women and Child"])
        category_string = ", ".join(raw_cats) if isinstance(raw_cats, list) else raw_cats
        
        batch = (
            f.get("schemeName", "Untitled"),
            f.get("briefDescription", "No description."),
            category_string,
            f.get("eligibilityCriteria", "Check official portal."),
            f.get("ministryName", "Central")
        )
        cursor.execute(query, batch)
        count += cursor.rowcount
    
    db.commit()
    cursor.close()
    db.close()
    print(f"✅ Successfully inserted {count} schemes.")

if __name__ == "__main__":
    import_women()