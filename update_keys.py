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
  "services_desc": "Comprehensive logistics solutions tailored for your needs.",
  "freight_transport": "Freight Transport",
  "freight_transport_desc": "Reliable transportation for all types of cargo across the country.",
  "real_time_tracking": "Real-time Tracking",
  "real_time_tracking_desc": "Monitor your shipment's progress with our advanced GPS tracking system.",
  "secure_logistics": "Secure Logistics",
  "secure_logistics_desc": "Your cargo is handled with care and protected by our secure systems.",
  "for_transporters_desc": "Join our network and grow your logistics business.",
  "grow_your_business": "Grow Your Business",
  "grow_your_business_desc": "Access a wide range of shipping requests and expand your client base.",
  "easy_management": "Easy Management",
  "easy_management_desc": "Manage your fleet and assignments efficiently through our dashboard.",
  "for_businesses_desc": "Optimize your supply chain with our reliable transport network.",
  "streamline_logistics": "Streamline Logistics",
  "streamline_logistics_desc": "Reduce complexity and improve efficiency in your shipping operations.",
  "cost_effective": "Cost Effective",
  "cost_effective_desc": "Get competitive pricing and reduce your overall logistics costs.",
  "pricing_desc": "Simple and transparent pricing for all our services.",
  "transparent_pricing": "Transparent Pricing",
  "transparent_pricing_desc": "No hidden fees. You only pay for what you use.",
  "legal_desc": "Legal information and policies for using CargoConnect.",
  "privacy_policy_content": "We value your privacy. This policy explains how we collect and use your data.",
  "terms_of_service_content": "By using our services, you agree to these terms and conditions.",
  "edit": "Edit"
}

am_keys = {
  "services_desc": "ለእርስዎ ፍላጎት የተዘጋጁ አጠቃላይ የሎጂስቲክስ መፍትሄዎች።",
  "freight_transport": "የጭነት መጓጓዣ",
  "freight_transport_desc": "በሀገሪቱ ውስጥ ለሁሉም ዓይነት ጭነቶች አስተማማኝ መጓጓዣ።",
  "real_time_tracking": "የቀጥታ ክትትል",
  "real_time_tracking_desc": "የእኛን የላቀ የጂፒኤስ ክትትል ስርዓት በመጠቀም የጭነትዎን ሂደት ይከታተሉ።",
  "secure_logistics": "ደህንነቱ የተጠበቀ ሎጂስቲክስ",
  "secure_logistics_desc": "ጭነትዎ በጥንቃቄ ይያዛል እና በደህንነት ስርዓቶቻችን ይጠበቃል።",
  "for_transporters_desc": "የእኛን አውታረ መረብ ይቀላቀሉ እና የሎጂስቲክስ ንግድዎን ያሳድጉ።",
  "grow_your_business": "ንግድዎን ያሳድጉ",
  "grow_your_business_desc": "ሰፊ የጭነት ጥያቄዎችን ያግኙ እና የደንበኛዎን መሠረት ያስፋፉ።",
  "easy_management": "ቀላል አስተዳደር",
  "easy_management_desc": "የእርስዎን ተሽከርካሪዎች እና ስራዎች በዳሽቦርዳችን በኩል በብቃት ያስተዳድሩ።",
  "for_businesses_desc": "በእኛ አስተማማኝ የትራንስፖርት አውታር የአቅርቦት ሰንሰለትዎን ያሻሽሉ።",
  "streamline_logistics": "ሎጂስቲክስን ያቀላጥፉ",
  "streamline_logistics_desc": "በመላኪያ ስራዎችዎ ውስጥ ውስብስብነትን ይቀንሱ እና ቅልጥፍናን ያሻሽሉ።",
  "cost_effective": "ወጪ ቆጣቢ",
  "cost_effective_desc": "ተወዳዳሪ ዋጋ ያግኙ እና አጠቃላይ የሎጂስቲክስ ወጪዎን ይቀንሱ።",
  "pricing_desc": "ለሁሉም አገልግሎቶቻችን ቀላል እና ግልጽ ዋጋ።",
  "transparent_pricing": "ግልጽ የዋጋ አሰጣጥ",
  "transparent_pricing_desc": "ምንም የተደበቁ ክፍያዎች የሉም። ለሚጠቀሙት ብቻ ይከፍላሉ።",
  "legal_desc": "ካርጎ ኮኔክትን ለመጠቀም ህጋዊ መረጃ እና ፖሊሲዎች።",
  "privacy_policy_content": "ለእርስዎ ግላዊነት ዋጋ እንሰጣለን። ይህ ፖሊሲ የእርስዎን ውሂብ እንዴት እንደምንሰበስብ እና እንደምንጠቀም ያብራራል።",
  "terms_of_service_content": "አገልግሎቶቻችንን በመጠቀም በእነዚህ ውሎች እና ሁኔታዎች ይስማማሉ።",
  "edit": "አስተካክል"
}

update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\en.json', en_keys)
update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\am.json', am_keys)
