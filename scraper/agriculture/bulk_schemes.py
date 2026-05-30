import requests
import json
import time

headers = {
    'accept': 'application/json, text/plain, */*',
    'origin': 'https://www.myscheme.gov.in',
    'x-api-key': 'tYTy5eEhlu9rFjyxuCr7ra7ACp4dv1RH8gWuHTDc',
    'user-agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1',
}

all_items = []
total_to_fetch = 900 # Total items we want
batch_size = 100

print("🚀 Starting multi-batch fetch...")

for i in range(0, total_to_fetch, batch_size):
    print(f"   📥 Fetching items {i} to {i + batch_size}...")
    params = {
        'lang': 'en',
        'q': '[{"identifier":"schemeCategory","value":"Agriculture,Rural & Environment"}]',
        'from': str(i),
        'size': str(batch_size), 
    }
    
    response = requests.get('https://api.myscheme.gov.in/search/v6/schemes', params=params, headers=headers)
    
    if response.status_code == 200:
        batch_data = response.json().get("data", {}).get("hits", {}).get("items", [])
        if not batch_data: break
        all_items.extend(batch_data)
        time.sleep(1) # Be nice to the server
    else:
        print(f"❌ Error at batch {i}: {response.status_code}")
        break

# Save all gathered data
final_payload = {"data": {"hits": {"items": all_items}}}
with open('scraper/agriculture/data_dump.json', 'w', encoding='utf-8') as f:
    json.dump(final_payload, f, indent=4)

print(f"\n✅ Finished! Captured {len(all_items)} total schemes into data_dump.json.")