<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Models\Corporate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use PDF;

class ImportReferral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:referral';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import referral data from crm big data v1';

    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected ReferralRepositoryInterface $referralRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected UserRepositoryInterface $userRepository;
    protected InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository;
    protected ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository;
    use CreateInvoiceIdTrait;
    use CreateCustomPrimaryKeyTrait;


    public function __construct(CorporateRepositoryInterface $corporateRepository, CorporatePicRepositoryInterface $corporatePicRepository, ReferralRepositoryInterface $referralRepository, ReceiptRepositoryInterface $receiptRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, UserRepositoryInterface $userRepository, InvoiceAttachmentRepositoryInterface $invoiceAttachmentRepository, ReceiptAttachmentRepositoryInterface $receiptAttachmentRepository)
    {
        parent::__construct();
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->referralRepository = $referralRepository;
        $this->receiptRepository = $receiptRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->userRepository = $userRepository;
        $this->invoiceAttachmentRepository = $invoiceAttachmentRepository;
        $this->receiptAttachmentRepository = $receiptAttachmentRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $partners = $this->referralRepository->getReferralFromV1();
        $progressBar = $this->output->createProgressBar($partners->count());
        $progressBar->start();
        DB::beginTransaction();
        try {

            foreach ($partners as $partner) {
                # if pt name from v1 does not exist on v2
                if (!$master = $this->corporateRepository->getCorporateByName($partner->pt_ins ? $partner->pt_ins : $partner->pt_name)) {
                    // $this->info(json_encode($partner));
                    $corp_phone = $this->getValueWithoutSpace($partner->pt_phone);
                    if ($corp_phone != NULL) {
                        $corp_phone = str_replace('-', '', $corp_phone);
                        $corp_phone = str_replace(' ', '', $corp_phone);
                        $corp_phone = str_replace('.', '', $corp_phone);
                        $corp_phone = str_replace('–', '', $corp_phone);
                        $corp_phone = str_replace(array('(', ')'), '', $corp_phone);
                        $corp_phone = str_replace(array('[', ']'), '', $corp_phone);

                        switch (substr($corp_phone, 0, 1)) {

                            case 0:
                                $corp_phone = "+62" . substr($corp_phone, 1);
                                break;

                            case 6:
                                $corp_phone = "+" . $corp_phone;
                                break;

                            case "+":
                                $corp_phone = $corp_phone;
                                break;

                            default:
                                $corp_phone = "+62" . $corp_phone;
                        }
                    }

                    # insert into corporate master data
                    $last_id = Corporate::max('corp_id');
                    $corp_id_without_label =  $last_id ? $this->remove_primarykey_label($last_id, 5) : '0000';
                    $corp_id_with_label = 'CORP-' . $this->add_digit($corp_id_without_label + 1, 4);

                    $partnerDetails = [
                        'corp_id' => $corp_id_with_label,
                        'corp_name' => $partner->pt_ins ? $partner->pt_ins : $partner->pt_name,
                        'corp_industry' => null,
                        'corp_mail' => $this->getValueWithoutSpace($partner->pt_email),
                        'corp_phone' => $corp_phone,
                        'corp_insta' => null,
                        'corp_site' => null,
                        'corp_region' => null,
                        'corp_address' => $this->getValueWithoutSpace($partner->pt_address),
                        'corp_note' => null,
                        'corp_password' => null,
                        'country_type' => $partner->pt_name == 'Lumiere Education' ? 'Overseas' : 'Indonesia',
                        'type' => 'Corporate',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    $master = $this->corporateRepository->createCorporate($partnerDetails);
                }


                // foreach ($receipts as $receipt) {

                $user = $this->userRepository->getUserByFullNameOrEmail('Theresya Afila Enfent', 'theresya.afila@all-inedu.com');


                if (isset($partner->receipt)) {

                    $companyDetail = [
                        'name' => env('ALLIN_COMPANY'),
                        'address' => env('ALLIN_ADDRESS'),
                        'address_dtl' => env('ALLIN_ADDRESS_DTL'),
                        'city' => env('ALLIN_CITY')
                    ];

                    // $this->info(json_encode($this->referralRepository->getReferralByCorpIdAndDate($master->corp_id, $partner->receipt->receipt_date)));
                    foreach ($partner->receipt as $receipt) {
                        // $this->info(json_encode($receipt));
                        if (!$referral = $this->referralRepository->getReferralByCorpIdAndDate($master->corp_id, $receipt->receipt_date)) {
                            // $this->info('>>');
                            $referralDetails = [
                                'partner_id' => $master->corp_id,
                                'empl_id' => $user->id,
                                'referral_type' => 'Out',
                                'additional_prog_name' => 'Referral',
                                'number_of_student' => 0,
                                'currency' => 'IDR',
                                'curs_rate' => null,
                                'revenue' => $receipt->receipt_amount,
                                'ref_date' => $receipt->receipt_date,
                                'notes' => null,
                                'created_at' => $receipt->receipt_date,
                                'updated_at' => $receipt->receipt_date,
                            ];
                            $referral = $this->referralRepository->createReferral($referralDetails);
                        }

                        $inv_id = str_replace('REC', 'INV', $receipt->receipt_id);
                        // $this->info(json_encode($this->invoiceB2bRepository->getInvoiceB2bByInvId($inv_id)));
                        if ($this->invoiceB2bRepository->getInvoiceB2bByInvId($inv_id)->count() == 0) {
                            !$invoice = $this->invoiceB2bRepository->getInvoiceB2bByInvId($inv_id)->first();
                            $invoiceDetails = [
                                'invb2b_id' => $inv_id,
                                'schprog_id' => null,
                                'partnerprog_id' => null,
                                'ref_id' => $referral->id,
                                'invb2b_price' => null,
                                'invb2b_priceidr' => $receipt->receipt_amount,
                                'invb2b_participants' => 0,
                                'invb2b_disc' => 0,
                                'invb2b_totprice' => 0,
                                'invb2b_totpriceidr' => $receipt->receipt_amount,
                                'invb2b_words' => null,
                                'invb2b_wordsidr' => $receipt->receipt_words,
                                'invb2b_date' => $receipt->receipt_date,
                                'invb2b_duedate' => $receipt->receipt_date,
                                'invb2b_pm' => 'Full Payment',
                                'invb2b_notes' => null,
                                'invb2b_tnc' => null,
                                'invb2b_status' => $receipt->receipt_status,
                                'curs_rate' => null,
                                'currency' => 'idr',
                                'is_full_amount' => 0,
                                'created_at' => $receipt->receipt_date,
                                'updated_at' => $receipt->receipt_date,
                            ];

                            $invoice = $this->invoiceB2bRepository->createInvoiceB2b($invoiceDetails);

                            $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceIdentifier('B2B', $invoice->invb2b_id);

                            if (count($attachment) == 0) {
                                $file_name = str_replace('/', '_', $invoice->invb2b_id) . '_idr.pdf'; # 0001_INV_JEI_EF_I_23_idr.pdf
                                $path = 'uploaded_file/invoice/referral/';
                                $attachment = $this->invoiceAttachmentRepository->getInvoiceAttachmentByInvoiceCurrency('B2B', $invoice->invb2b_id, 'idr');

                                $attachmentDetails = [
                                    'invb2b_id' => $invoice->invb2b_id,
                                    'currency' => 'idr',
                                    'attachment' => 'storage/' . $path . $file_name,
                                    'sign_status' => 'signed',
                                    'approve_date' => Carbon::now(),
                                    'send_to_client' => 'not sent'
                                ];

                                $pdf = PDF::loadView('pages.invoice.referral.export.invoice-pdf', [
                                    'invoiceRef' => $invoice,
                                    'currency' => 'idr',
                                    'companyDetail' => $companyDetail
                                ]);

                                # Generate PDF file
                                $content = $pdf->download();
                                Storage::disk('public')->put($path . $file_name, $content);

                                # if attachment exist then update attachement else insert attachement
                                if (isset($attachment)) {
                                    $this->invoiceAttachmentRepository->updateInvoiceAttachment($attachment->id, $attachmentDetails);
                                } else {
                                    $this->invoiceAttachmentRepository->createInvoiceAttachment($attachmentDetails);
                                }
                            }

                            if (!$newReceipt = $this->receiptRepository->getReceiptByReceiptId($receipt->receipt_id)) {

                                $receiptDetails = [
                                    'receipt_id' => $receipt->receipt_id,
                                    'receipt_cat' => 'referral',
                                    'inv_id' => null,
                                    'invdtl_id' => null,
                                    'invb2b_id' => $inv_id,
                                    'receipt_method' => 'Wire Transfer',
                                    'receipt_cheque' => null,
                                    'receipt_amount' => null,
                                    'receipt_words' => null,
                                    'receipt_amount_idr' => $receipt->receipt_amount,
                                    'receipt_words_idr' => $receipt->receipt_words,
                                    'receipt_notes' => null,
                                    'receipt_status' => $receipt->receipt_status,
                                    'download_other' => 0,
                                    'download_idr' => 0,
                                    'created_at' => $receipt->receipt_date,
                                    'updated_at' => $receipt->receipt_date,
                                ];

                                $newReceipt = $this->receiptRepository->createReceipt($receiptDetails);

                                $attachmentReceipt = $this->receiptAttachmentRepository->getReceiptAttachmentByReceiptId($receipt->receipt_id, 'idr');
                                $invb2b_id = $newReceipt->invb2b_id;
                                $invoiceRef = $this->invoiceB2bRepository->getInvoiceB2bByInvId($invb2b_id)->first();


                                if (!isset($attachmentReceipt)) {
                                    $file_name = str_replace('/', '_', $newReceipt->receipt_id) . '_idr.pdf'; # 0001_REC_JEI_EF_I_23_idr.pdf
                                    $path = 'uploaded_file/receipt/referral/';

                                    $receiptAttachments = [
                                        'receipt_id' => $newReceipt->receipt_id,
                                        'attachment' => 'storage/' . $path . $file_name,
                                        'currency' => 'idr',
                                        'sign_status' => 'signed',
                                        'approve_date' => Carbon::now(),
                                        'send_to_client' => 'not sent'
                                    ];

                                    $pdf = PDF::loadView('pages.receipt.referral.export.receipt-pdf', ['receiptRef' => $newReceipt, 'invoiceRef' => $invoiceRef, 'currency' => 'idr', 'companyDetail' => $companyDetail]);


                                    # Generate PDF file
                                    $content = $pdf->download();
                                    Storage::disk('public')->put($path . $file_name, $content);


                                    $this->receiptAttachmentRepository->createReceiptAttachment($receiptAttachments);
                                }
                            }
                        }
                    }
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            DB::commit();
            Log::info('Import Referral works fine');
        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('There\'s something wrong with import referral : ' . $e->getMessage() . $e->getLine());
        }
        return Command::SUCCESS;
    }

    private function getValueWithoutSpace($value)
    {
        return $value == "" || $value == "-" || $value == "tidak ada" || $value == "no contact" || $value == "0000-00-00" || $value == "0000-00-00 00:00:00" || $value == 'N/A' ? NULL : $value;
    }
}
