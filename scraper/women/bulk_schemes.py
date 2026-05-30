import requests
import json
import os

def fetch_women_schemes():
    url = 'https://api.myscheme.gov.in/search/v6/schemes'
    headers = {
        "Accept": "application/json, text/plain, */*",
        "Origin": "https://www.myscheme.gov.in",
        "Referer": "https://www.myscheme.gov.in/",
        "User-Agent": "Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1",
        "x-api-key": "tYTy5eEhlu9rFjyxuCr7ra7ACp4dv1RH8gWuHTDc"
    }

    all_items = []
    # Fetch in batches of 100
    for offset in range(0, 600, 100): 
        params = {
            'lang': 'en',
            'q': '[{"identifier":"schemeCategory","value":"Women and Child"}]',
            'from': str(offset),
            'size': '100',
        }
        
        print(f"📡 Fetching batch at {offset}...")
        response = requests.get(url, params=params, headers=headers)
        
        if response.status_code == 200:
            data = response.json()
            items = data.get("data", {}).get("hits", {}).get("items", [])
            if not items: break
            all_items.extend(items)
        else:
            print(f"❌ Error at offset {offset}")
            break

    output_path = os.path.join(os.path.dirname(__file__), "data_dump.json")
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump({"data": {"hits": {"items": all_items}}}, f, indent=4)
    print(f"✅ Saved {len(all_items)} women-centric schemes.")

if __name__ == "__main__":
    fetch_women_schemes()