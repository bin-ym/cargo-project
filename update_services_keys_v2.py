import json
import os

def update_json(file_path, new_keys):
    if not os.path.exists(file_path):
        print(f"File {file_path} not found")
        return
    
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    data.update(new_keys)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
    print(f"Updated {file_path}")

en_keys = {
    "customer_services": "Customer Services",
    "anywhere_delivery": "Anywhere Delivery",
    "anywhere_delivery_desc": "From urban centers to remote areas, we deliver your cargo wherever it needs to go.",
    "transporter_benefits": "Transporter Benefits",
    "grow_with_us": "Grow With Us",
    "grow_with_us_desc": "Scale your business with our advanced tools and wide customer reach.",
    "enterprise_solutions": "Enterprise Solutions",
    "logistics_optimization": "Logistics Optimization",
    "logistics_optimization_desc": "Advanced routing and fleet management to reduce your operational costs.",
    "base_rate": "Base Rate",
    "platform_fee": "Platform Fee"
}

am_keys = {
    "customer_services": "የደንበኞች አገልግሎት",
    "anywhere_delivery": "በማንኛውም ቦታ ማድረስ",
    "anywhere_delivery_desc": "ከከተማ ማእከላት እስከ ራቅ ያሉ አካባቢዎች፣ ጭነትዎን ወደ ሚፈለገው ቦታ እናደርሳለን።",
    "transporter_benefits": "የአጓጓዦች ጥቅሞች",
    "grow_with_us": "ከእኛ ጋር ያድጉ",
    "grow_with_us_desc": "የእኛን የላቁ መሳሪያዎች እና ሰፊ የደንበኛ ተደራሽነት በመጠቀም ንግድዎን ያስፋፉ።",
    "enterprise_solutions": "የድርጅት መፍትሄዎች",
    "logistics_optimization": "የሎጂስቲክስ ማመቻቸት",
    "logistics_optimization_desc": "የስራ ማስኬጃ ወጪዎችዎን ለመቀነስ የላቀ የመንገድ ምርጫ እና የተሽከርካሪ አስተዳደር።",
    "base_rate": "መነሻ ዋጋ",
    "platform_fee": "የመድረክ ክፍያ"
}

update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\en.json', en_keys)
update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\am.json', am_keys)
