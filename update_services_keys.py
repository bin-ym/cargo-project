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
    "services_title": "Our Services",
    "services_subtitle": "Comprehensive logistics solutions for every need.",
    "for_customers_title": "For Customers",
    "for_customers_desc": "Ship anything, anywhere, with full transparency and ease.",
    "customer_service_1": "Instant Quotes: Get transparent pricing based on distance and weight.",
    "customer_service_2": "Real-time Tracking: Monitor your cargo's journey on a live map.",
    "customer_service_3": "Secure Payments: Pay safely through integrated Chapa gateway.",
    "for_transporters_title": "For Transporters",
    "for_transporters_desc": "Join Ethiopia's fastest-growing logistics network and maximize your earnings.",
    "transporter_service_1": "Steady Stream of Jobs: Access a wide range of delivery requests daily.",
    "transporter_service_2": "Fleet Management: Easily manage your vehicles and assignments.",
    "transporter_service_3": "Fast Payouts: Receive your earnings quickly after successful deliveries.",
    "for_businesses_title": "For Businesses",
    "for_businesses_desc": "Optimize your supply chain with our enterprise-grade logistics platform.",
    "business_service_1": "Bulk Shipping: Manage large-scale cargo movements effortlessly.",
    "business_service_2": "Detailed Analytics: Track your logistics spending and efficiency.",
    "business_service_3": "Priority Support: Dedicated account management for business partners.",
    "pricing_title": "Transparent Pricing",
    "pricing_subtitle": "Fair and predictable costs for all your shipping needs.",
    "pricing_base_rate": "Base Rate: Starting from 500 ETB for local deliveries.",
    "pricing_distance_rate": "Distance: Calculated per kilometer based on the most efficient route.",
    "pricing_weight_rate": "Weight: Flexible pricing tiers for different cargo sizes.",
    "pricing_commission": "Commission: Transparent 20% platform fee for transporters."
}

am_keys = {
    "services_title": "አገልግሎቶቻችን",
    "services_subtitle": "ለእያንዳንዱ ፍላጎት አጠቃላይ የሎጂስቲክስ መፍትሄዎች።",
    "for_customers_title": "ለደንበኞች",
    "for_customers_desc": "ማንኛውንም ነገር፣ በማንኛውም ቦታ፣ ሙሉ ግልጽነት እና ምቾት ይላኩ።",
    "customer_service_1": "ፈጣን ዋጋ፡ በርቀት እና በክብደት ላይ የተመሰረተ ግልጽ ዋጋ ያግኙ።",
    "customer_service_2": "የቀጥታ ክትትል፡ የጭነትዎን ጉዞ በቀጥታ ካርታ ላይ ይከታተሉ።",
    "customer_service_3": "ደህንነቱ የተጠበቀ ክፍያ፡ በተዋሃደ የቻፓ መግቢያ በኩል በደህና ይክፈሉ።",
    "for_transporters_title": "ለአጓጓዦች",
    "for_transporters_desc": "የኢትዮጵያን ፈጣን እያደገ ያለውን የሎጂስቲክስ አውታር ይቀላቀሉ እና ገቢዎን ያሳድጉ።",
    "transporter_service_1": "የማይቋረጥ ስራ፡ በየቀኑ ሰፊ የማድረስ ጥያቄዎችን ያግኙ።",
    "transporter_service_2": "የተሽከርካሪዎች አስተዳደር፡ ተሽከርካሪዎችዎን እና ስራዎችዎን በቀላሉ ያስተዳድሩ።",
    "transporter_service_3": "ፈጣን ክፍያ፡ ስራውን በተሳካ ሁኔታ ካጠናቀቁ በኋላ ገቢዎን በፍጥነት ያግኙ።",
    "for_businesses_title": "ለንግድ ድርጅቶች",
    "for_businesses_desc": "በእኛ የሎጂስቲክስ መድረክ የአቅርቦት ሰንሰለትዎን ያሻሽሉ።",
    "business_service_1": "በጅምላ መላክ፡ መጠነ ሰፊ የጭነት እንቅስቃሴዎችን ያለልፋት ያስተዳድሩ።",
    "business_service_2": "ዝርዝር ትንታኔ፡ የሎጂስቲክስ ወጪዎን እና ቅልጥፍናዎን ይከታተሉ።",
    "business_service_3": "ቅድሚያ የሚሰጠው ድጋፍ፡ ለንግድ አጋሮች የተለየ የደንበኛ ድጋፍ።",
    "pricing_title": "ግልጽ የዋጋ አሰጣጥ",
    "pricing_subtitle": "ለሁሉም የመላኪያ ፍላጎቶችዎ ፍትሃዊ እና ሊገመት የሚችል ወጪ።",
    "pricing_base_rate": "መነሻ ዋጋ፡ ለአካባቢ ማድረሻ ከ500 ብር ጀምሮ።",
    "pricing_distance_rate": "ርቀት፡ በጣም ቀልጣፋ በሆነው መንገድ በኪሎሜትር ይሰላል።",
    "pricing_weight_rate": "ክብደት፡ ለተለያዩ የጭነት መጠኖች ተለዋዋጭ የዋጋ ደረጃዎች።",
    "pricing_commission": "ኮሚሽን፡ ለአጓጓዦች ግልጽ የሆነ 20% የመድረክ ክፍያ።"
}

update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\en.json', en_keys)
update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\am.json', am_keys)
