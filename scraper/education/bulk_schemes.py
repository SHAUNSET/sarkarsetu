import requests
import json
import time
import os

# Configuration
CATEGORY = "Education & Learning"
OUTPUT_FILE = "data_dump.json" # Saves in the same folder as this script
TOTAL_TO_FETCH = 1200 
BATCH_SIZE = 100

headers = {
    'accept': 'application/json, text/plain, */*',
    'origin': 'https://www.myscheme.gov.in',
    'x-api-key': 'tYTy5eEhlu9rFjyxuCr7ra7ACp4dv1RH8gWuHTDc',
    'user-agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1',
}

all_items = []

print(f"🚀 Starting fetch for category: {CATEGORY}")

for i in range(0, TOTAL_TO_FETCH, BATCH_SIZE):
    print(f"   📥 Fetching items {i} to {i + BATCH_SIZE}...")
    
    params = {
        'lang': 'en',
        'q': f'[{{"identifier":"schemeCategory","value":"{CATEGORY}"}}]',
        'from': str(i),
        'size': str(BATCH_SIZE), 
    }
    
    try:
        response = requests.get('https://api.myscheme.gov.in/search/v6/schemes', params=params, headers=headers)
        
        if response.status_code == 200:
            batch_data = response.json().get("data", {}).get("hits", {}).get("items", [])
            if not batch_data: 
                print("   ✅ No more items found. Ending fetch.")
                break
            all_items.extend(batch_data)
            time.sleep(1.5) # Slight delay to be respectful to the server
        else:
            print(f"❌ Error at batch {i}: {response.status_code}")
            break
            
    except Exception as e:
        print(f"❌ Connection error: {e}")
        break

# Save to the current folder
final_payload = {"data": {"hits": {"items": all_items}}}
with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
    json.dump(final_payload, f, indent=4)

print(f"\n✅ Finished! Captured {len(all_items)} total schemes into {OUTPUT_FILE}.")