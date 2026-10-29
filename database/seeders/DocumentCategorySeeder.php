<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Relationship Verification',
                'name_np' => 'नाता प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Citizenship Certificate and Copy Recommendation',
                'name_np' => 'नागरिकता तथा नागरिकताको प्रतिलिपि लिनका लागि सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Rent Tax Assessment Recommendation',
                'name_np' => 'बहाल करको लेखाजोखा सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Tenant Registration Cancellation Recommendation',
                'name_np' => 'मोही लगत कट्टाको सिफारिस गर्ने',
            ],
            [
                'name_en' => 'House and Land Tax Assessment Recommendation',
                'name_np' => 'घर जग्गा करको लेखाजोखा सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Date of Birth Verification',
                'name_np' => 'जन्म मिति प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Recommendation for Business Closure or Non-Operation',
                'name_np' => 'व्यापारव्यवसाय बन्द भएको, सञ्चालन नभएको वा व्यापार व्यवसाय हुँदै नभएको सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Application Registration for Settlement Agreement Recommendation',
                'name_np' => 'मिलापत्र कागज गराउने निवेदन दर्ता गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Marriage and Unmarried Status Verification',
                'name_np' => 'विवाह प्रमाणित तथा अविवाहित प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Free or Paid Medical Treatment Recommendation',
                'name_np' => 'निःशुल्क वा सशुल्क स्वास्थ्य उपचारको सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Recommendation and Verification in English',
                'name_np' => 'अङ्ग्रेजी माध्यममा सिफारिस तथा प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'House Demolition Verification',
                'name_np' => 'घर पाताल प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Personal Details Verification',
                'name_np' => 'व्यक्तिगत विवरण प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Recommendation to Register a House on the Land Ownership Certificate',
                'name_np' => 'पुर्जामा घर कायम गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Recommendation Verifying Different Names, Surnames or Dates of Birth Belong to the Same Person',
                'name_np' => 'फरक, फरक नाम, थर, जन्म मिति तथा प्रमाणित दुवै नाम गरेको व्यक्ति एकै हो भन्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Name, Surname and Date of Birth Correction Recommendation',
                'name_np' => 'नाम, थर, जन्म मिति संशोधनको सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Lost Land Ownership Certificate Recommendation',
                'name_np' => 'जग्गा धनीपुर्जा हराएको सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Document and Consent Letter Verification',
                'name_np' => 'कागज र मञ्जुरीनामा प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Land Parcel Subdivision Recommendation',
                'name_np' => 'कित्ताकाट गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Guardian Verification and Individual or Institutional Guardian Recommendation',
                'name_np' => 'संरक्षक प्रमाणित तथा संस्थागत र व्यक्तिगत संरक्षक सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Relationship Verification with a Living Person and Proof of Being Alive',
                'name_np' => 'जीवितसँगको नाता प्रमाणित गर्ने। जीवित रहेको सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Legal Heir or Successor Verification',
                'name_np' => 'हकवाला वा हकदार प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Ownership Transfer Recommendation',
                'name_np' => 'नामसारी गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Land Rights Recommendation',
                'name_np' => 'जग्गाको हक सम्बन्धमा सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Relationship Verification with a Deceased Person and Field Inquiry Recommendation',
                'name_np' => 'मृतकसँगको नाता प्रमाणित तथा सर्जमिन सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Industry Relocation Recommendation',
                'name_np' => 'उद्योग ठाउँसारी गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Recommendation and Permission to Establish a Pre-Primary School',
                'name_np' => 'पूर्व प्राथमिक विद्यालय खोल्ने सिफारिस र अनुमति दिने',
            ],
            [
                'name_en' => 'Land Valuation Recommendation and Verification',
                'name_np' => 'जग्गा मूल्याङ्कन सिफारिस प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'Recommendation to Add Classes at a School',
                'name_np' => 'विद्यालयको कक्षा थप गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Recommendation for Care and Maintenance',
                'name_np' => 'पालनपोषणको लागि सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Marital Naturalized Citizenship Recommendation',
                'name_np' => 'वैवाहिक अंगीकृत नागरिकता सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Financial Hardship or Financial Solvency Verification',
                'name_np' => 'आर्थिक अवस्था कमजोर वा विपन्नता प्रमाणित गर्ने वा आर्थिक अवस्था बलियो वा सम्पन्नता प्रमाणित गर्ने',
            ],
            [
                'name_en' => 'School Relocation Recommendation',
                'name_np' => 'विद्यालय ठाउँसारी गर्ने सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Water Supply and Electricity Connection Recommendation',
                'name_np' => 'धारा तथा विद्युत जडानको लागि सिफारिस गर्ने',
            ],
            [
                'name_en' => 'Other Recommendations or Verifications under Delegated Authority in Accordance with Applicable Law',
                'name_np' => 'प्रचलित कानून अनुसार प्रत्यायोजित अधिकार बमोजिमको अन्य सिफारिस वा प्रमाणित गर्ने',
            ],
        ];

        $now = now();

        foreach ($categories as &$category) {
            $category['created_at'] = $now;
            $category['updated_at'] = $now;
        }

        unset($category);

        DB::table('document_categories')->upsert(
            $categories,
            ['name_en'],
            ['name_np', 'updated_at']
        );
    }
}