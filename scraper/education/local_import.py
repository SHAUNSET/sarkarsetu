import json
import mysql.connector
import os

# Configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'sarkarsetu'
}

def import_schemes():
    print("🚀 Starting Education import (Appending to DB)...")
    
    # 1. Location-aware pathing to fix your "File not found" error
    script_dir = os.path.dirname(os.path.abspath(__file__))
    file_path = os.path.join(script_dir, "data_dump.json")
    
    try:
        db = mysql.connector.connect(**DB_CONFIG)
        cursor = db.cursor()
    except Exception as e:
        print(f"❌ Connection error: {e}")
        return

    # 2. Load the JSON data using the correct absolute path
    try:
        with open(file_path, "r", encoding="utf-8") as f:
            data = json.load(f)
    except Exception as e:
        print(f"❌ File read error: {e}")
        return

    items = data.get("data", {}).get("hits", {}).get("items", [])
    print(f"📦 Found {len(items)} schemes. Inserting into database...")

    # 3. Prepared Query
    query = """
        INSERT INTO schemes (title, description, category, eligibility, ministry) 
        VALUES (%s, %s, %s, %s, %s)
    """
    
    # 4. Batch Insertion
    batch = []
    for item in items:
        fields = item.get("fields", {})
        
        # Extract and format fields
        title = fields.get("schemeName", "Untitled")
        desc = fields.get("briefDescription", "No description provided.")
        # Ensure category is a string
        cat_list = fields.get("schemeCategory", [])
        cat = ", ".join(cat_list) if isinstance(cat_list, list) else str(cat_list)
        
        elig = fields.get("eligibilityCriteria", "Check official portal.")
        
        # Ministry and State concatenation
        states = fields.get("beneficiaryState", [])
        state_str = ", ".join(states) if states else "All India"
        ministry = fields.get("ministryName", "Central Government")
        ministry_info = f"{ministry} ({state_str})"
        
        batch.append((title, desc, cat, elig, ministry_info))
        
        # Execute in chunks of 50
        if len(batch) >= 50:
            cursor.executemany(query, batch)
            batch = []
    
    # Final cleanup for remaining items
    if batch:
        cursor.executemany(query, batch)
    
    db.commit()
    cursor.close()
    db.close()
    print(f"✅ Success! {len(items)} education schemes added to database.")

if __name__ == "__main__":
    import_schemes()
