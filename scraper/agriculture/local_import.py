import json
import mysql.connector

# Configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'sarkarsetu'
}

def import_schemes():
    print("🚀 Starting enhanced import...")
    
    try:
        db = mysql.connector.connect(**DB_CONFIG)
        cursor = db.cursor()
    except Exception as e:
        print(f"❌ Connection error: {e}")
        return

    # Clear table to ensure fresh, accurate data
    cursor.execute("TRUNCATE TABLE schemes;")
    
    try:
        with open("scraper/agriculture/data_dump.json", "r", encoding="utf-8") as f:
            data = json.load(f)
    except Exception as e:
        print(f"❌ File read error: {e}")
        return

    items = data.get("data", {}).get("hits", {}).get("items", [])
    print(f"📦 Found {len(items)} schemes. Mapping fields...")

    query = """
        INSERT INTO schemes (title, description, category, eligibility, ministry) 
        VALUES (%s, %s, %s, %s, %s)
    """
    
    batch = []
    for item in items:
        fields = item.get("fields", {})
        
        # Enhanced Field Extraction
        title = fields.get("schemeName", "Untitled")
        desc = fields.get("briefDescription", "No description provided.")
        cat = ", ".join(fields.get("schemeCategory", []))
        
        # Extract Eligibility (If available in API, otherwise descriptive fallback)
        elig = fields.get("eligibilityCriteria", "Check official portal for eligibility details.")
        
        # Extract State/Ministry details
        states = fields.get("beneficiaryState", [])
        state_str = ", ".join(states) if states else "All India/Central"
        ministry = fields.get("ministryName", "Central Government")
        ministry_info = f"{ministry} ({state_str})"
        
        batch.append((title, desc, cat, elig, ministry_info))
        
        # Batch insert for speed
        if len(batch) >= 50:
            cursor.executemany(query, batch)
            batch = []
    
    # Final batch
    if batch:
        cursor.executemany(query, batch)
    
    db.commit()
    cursor.close()
    db.close()
    print(f"✅ Success! {len(items)} schemes imported with full details.")

if __name__ == "__main__":
    import_schemes()
