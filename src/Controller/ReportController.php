<?php

namespace App\Controller;

use App\Repository\MemberCodStatRepository;
use App\Repository\MemberCodTransferRepository;
use App\Repository\PcComApprovedRepository;
use App\Repository\TestAllParcelRepository;
use App\Repository\TestHybridOwnerRepository;
use App\Repository\TestMemberCodAllRepository;
use App\Repository\TestParcelSizeCollectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/report_cod", name="report_cod")
     */
    public function report_cod(
        Request $request,
        PaginatorInterface $paginator,
        MemberCodTransferRepository $memberCodTransferRepository,
        TestMemberCodAllRepository $testMemberCodAllRepository,
        TestAllParcelRepository $testAllParcelRepository
    )
    {
        $phoneregis = $this->session->get('phoneNo');
        if ($phoneregis != null) {
            $countParcelData = [];
            $countParcelRawData = $testAllParcelRepository->getCountDataBySenderPhoneAll($phoneregis);
            foreach ($countParcelRawData as $key => $val) {
                $countParcelData[$val['codReturn']][] = $val;
            }
            foreach ($countParcelData as $key => $val) {
                $total1[$key] = 0;
                foreach ($val as $k => $v) {
                    $total1[$key] = $total1[$key] + $v['codAmt'];
                    $countParcelData[$key] = [
                        'count' => count($val),
                        'amt' => $total1[$key]
                    ];
                }
            }
            $total_array = [
                'count' => 0,
                'amt' => 0
            ];
            foreach ($countParcelData as $key => $val) {
                $total_array['count'] = $total_array['count'] + $countParcelData[$key]['count'];
                $total_array['amt'] = $total_array['amt'] + $countParcelData[$key]['amt'];
            }
            $countParcelData['all'] = [
                'count' => $total_array['count'],
                'amt' => $total_array['amt']
            ];


            $cod_transfer = $memberCodTransferRepository->getDataByPhoneRegister($phoneregis);
            $total2 = 0;
            foreach ($cod_transfer as $key => $val) {
                foreach ($val as $k => $v) {
                    if ($k === 'ref') {
                        if ($cod_transfer[$key]['ref'] != null) {
                            $cod_transfer[$key]['ref'] = $this->cutSyntaxToWord($v);
                        } else {
                            $cod_transfer[$key]['ref'] = null;
                        }
                    }
                    if ($k === 'cod_amt') {
                        $cod_transfer[$key]['cod_amt'] = round($v, 2);
                    }
                }
                $total2 = $total2 + $val['cod_amt'];
            }
            $results = $paginator->paginate(
                $cod_transfer, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10
            );
            return $this->render('report/report_cod.html.twig', [
                'cod_data' => $countParcelData,
                'transfer_data' => $results,
                'total' => $total2
            ]);
        } else {
            return $this->redirect($this->generateUrl('app_login'));
        }
    }

    /**
     * @Route("/report_cod_all", name="report_cod_all")
     */
    public function report_cod_all(
        Request $request,
        PaginatorInterface $paginator,
        PcComApprovedRepository $pcComApprovedRepository
    )
    {
        $phoneregis = $this->session->get('phoneNo');
        if ($phoneregis != null) {
            $ref = $request->query->get('ref');
            $cod_stat = $pcComApprovedRepository->getDataByMemberIdAndRef($this->session->get('memberId'), $ref);

            $cod_result = [];

            foreach ($cod_stat as $key => $val) {
                $cod_result[] = $cod_stat[$key]['transferAmt'];
                foreach ($val as $k => $v) {
                    if ($k === 'orderphoneno') {
                        $cod_stat[$key]['orderphoneno'] = $this->doubleSix2Zero($v);
                    }
                }
            }
            $total = array('total' => array_sum($cod_result));
            $results = $paginator->paginate(
                $cod_stat, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10
            );
            return $this->render('report/report_cod_all.html.twig', [
                'cod_data' => $results,
                'total' => $total
            ]);
        } else {
            return $this->redirect($this->generateUrl('app_login'));
        }
    }

    /**
     * @Route("/report_all", name="report_all")
     */
    public function report_all(
        Request $request,
        PaginatorInterface $paginator,
        TestParcelSizeCollectRepository $testParcelSizeCollectRepository,
        TestAllParcelRepository $testAllParcelRepository,
        TestHybridOwnerRepository $hybridOwnerRepository
    )
    {
        $phoneregis = $this->session->get('phoneNo');
        if ($phoneregis != null) {

            $parcel_data = $testParcelSizeCollectRepository->findOneBy(array('senderPhoneno' => $phoneregis));

//            dd($parcel_data);

            $countRawData = $testAllParcelRepository->getCountStatusBySenderPhone($phoneregis);
            $countData = [];

            foreach ($countRawData as $key => $val) {
                $countData[$val['statusNameTh']][] = $val;
            }

            $dataResult = [];
            $status = [
                'สินค้าถึงปลายทาง' => 0,
                'สินค้าตีกลับ' => 0,
                'ยกเลิกรายการ' => 0,
                'จัดส่งแล้ว' => 0,
                'รอชำระเงิน' => 0,
                'ชำระเงินแล้ว' => 0
            ];
            foreach ($countData as $key => $val) {
                if (array_key_exists($key, $status)) {
                    $status[$key] = count($val);
                } else {
                    $status[$key] = 0;
                }
                $dataResult = [
                    'status' => $status,
                ];
            }

            $search = $request->query->get('search');
            $filter = $request->query->get('filter');
            $status = $request->query->get('status');
            $size = $request->query->get('size');
            $search_date = $request->query->get('search_date');
            $search_date_end = $request->query->get('date_end');
            if (isset($search)) {
                if (isset($filter) && isset($search)) {
                    if (strlen($search) > 3) {
                        if ($filter == 'all') {
                            $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSearchAll($phoneregis,
                                $search);
                        } elseif ($filter == 'waiting') {
                            $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSearchFilter($phoneregis,
                                $search, 'N');
                        } elseif ($filter == 'transfer') {
                            $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSearchFilter($phoneregis,
                                $search, 'Y');
                        } else {
                            $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSearch($phoneregis,
                                $search);
                        }
                    } else {
                        $this->addFlash('error', 'กรุณากรอกขั้นต้ำอย่างน้อย 3 ตัว');
                        return $this->redirect($this->generateUrl('report_all') . '?filter=' . $filter);
                    }
                } elseif (isset($size) && isset($search)) {
                    if (strlen($search) > 3) {
                        $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSearchSize($phoneregis,
                            $size, $search);
                    } else {
                        $this->addFlash('error', 'กรุณากรอกขั้นต้ำอย่างน้อย 3 ตัว');
                        return $this->redirect($this->generateUrl('report_all') . '?size=' . $size);
                    }
                } elseif (isset($status) && isset($search)) {
                    if (strlen($search) > 3) {
                        $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndStatusAndSearch($phoneregis,
                            $status, $search);
                    } else {
                        $this->addFlash('error', 'กรุณากรอกขั้นต้ำอย่างน้อย 3 ตัว');
                        return $this->redirect($this->generateUrl('report_all') . '?size=' . $size);
                    }
                } else {
                    if (strlen($search) > 3) {
                        $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSearch($phoneregis, $search);
                    } else {
                        $this->addFlash('error', 'กรุณากรอกขั้นต้ำอย่างน้อย 3 ตัว');
                        return $this->redirect($this->generateUrl('report_all'));
                    }
                }
            } elseif (isset($status)) {
                $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndStatus($phoneregis, $status);
            } elseif (isset($search_date)) {
                if ($search_date != null && $search_date_end != null) {
                    $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAndSendDate($phoneregis, $search_date, $search_date_end);
                } else {
                    $this->addFlash('error', 'กรุณาเลือกวันที่');
                    return $this->redirect($this->generateUrl('report_all'));
                }
            } else {
                if (isset($filter)) {
                    if ($filter == 'all') {
                        $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneAll($phoneregis);
                    } elseif ($filter == 'waiting') {
                        $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneFilter($phoneregis, 'N');
                    } elseif ($filter == 'transfer') {
                        $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneFilter($phoneregis, 'Y');
                    }
                } elseif (isset($size)) {
                    $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneFilterSize($phoneregis, $size);
                } else {
                    $allParcel_data = $testAllParcelRepository->getDataBySenderPhone($phoneregis);
                }
            }

            foreach ($allParcel_data as $key => $val) {
                $allParcel_data[$key]['name'] = $this->changeSyntaxData3($allParcel_data[$key]['recipientInfo']);
                foreach ($val as $k => $v) {
                    if ($k === 'recipientInfo') {
                        $allParcel_data[$key]['recipientInfo'] = $this->changeSyntaxData($v);
                    }
                }
            }

            $checkHybrid = $hybridOwnerRepository->count(array("tel" => $phoneregis));
            if($checkHybrid >= 1) {
                foreach ($allParcel_data as $key => $val) {
                    $allParcel_data[$key] = array_diff_key($allParcel_data[$key], ["sizePrice" => "xy"]);
                }
            }

            $results = $paginator->paginate(
                $allParcel_data, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10

            );
            return $this->render('report/report_all.html.twig', [
                'parcel_data' => $parcel_data,
                'allparcel_data' => $results,
                'countStatus' => $dataResult
            ]);
        } else {
            return $this->redirect($this->generateUrl('app_login'));
        }
    }

    /**
     * @Route("report_all/download", name="download_file")
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    function downloadFile(Request $request, TestAllParcelRepository $testAllParcelRepository)
    {
        date_default_timezone_set("Asia/Bangkok");
        $phoneregis = $this->session->get('phoneNo');
        if ($phoneregis != null) {
            $allParcel_data = $testAllParcelRepository->getDataBySenderPhoneForDownload($phoneregis,
                $request->query->get('startDate'), $request->query->get('endDate'), $request->query->get('status'));
            if (empty($allParcel_data)) {
                $this->addFlash('error', 'ไม่พบข้อมูล');
                return $this->redirect($this->generateUrl('report_all'));
            } else {
                foreach ($allParcel_data as $key => $val) {
                    foreach ($val as $k => $v) {
                        if ($k === 'sendmaildate') {
                            if ($v !== null) {
                                $allParcel_data[$key]['sendmaildate'] = $this->formatDate($v);
                            } else {
                                $allParcel_data[$key]['sendmaildate'] = '-';
                            }
                        }
                        if ($k === 'transactiondate') {
                            if ($v !== null) {
                                $allParcel_data[$key]['transactiondate'] = $this->formatDate($v);
                            } else {
                                $allParcel_data[$key]['transactiondate'] = '-';
                            }
                        }
                        if ($k === 'ffmProduct') {
                            if ($v == null) {
                                $allParcel_data[$key]['ffmProduct'] = '-';
                            }
                        }
                    }
                }
                $spreadsheet = new Spreadsheet();
                /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
                $sheet = $spreadsheet->getActiveSheet();

                // Set Topic
                $sheet->setCellValue('A1', 'วันที่จัดส่ง');
                $sheet->setCellValue('B1', 'TRACKING');
                $sheet->setCellValue('C1', 'ผู้รับ');
                $sheet->setCellValue('D1', 'COD/NOR');
                $sheet->setCellValue('E1', 'สถานะ');
                $sheet->setCellValue('F1', 'AREA');
                $sheet->setCellValue('G1', 'SIZE');
                $sheet->setCellValue('H1', 'ค่าส่ง');
                $sheet->setCellValue('I1', 'รายการสินค้า');
                $sheet->setCellValue('J1', 'ส่งสำเร็จ');
                $sheet->setCellValue('K1', 'จังหวัด');

                $sheet->getStyle('A1:K1')->getFont()->setBold(true);

                // Set Value
                for ($i = 0; $i < count($allParcel_data); $i++) {
                    $sheet->setCellValue('A' . (($i + 1) + 1), $allParcel_data[$i]['sendmaildate']);
                    $sheet->setCellValue('B' . (($i + 1) + 1), $allParcel_data[$i]['mailcode']);
                    $sheet->setCellValue('C' . (($i + 1) + 1), $allParcel_data[$i]['ordername']);
                    $sheet->setCellValue('D' . (($i + 1) + 1), $allParcel_data[$i]['transportType']);
                    $sheet->setCellValue('E' . (($i + 1) + 1), $allParcel_data[$i]['statusNameTh']);
                    $sheet->setCellValue('F' . (($i + 1) + 1), $allParcel_data[$i]['sizeName']);
                    $sheet->setCellValue('G' . (($i + 1) + 1), $allParcel_data[$i]['area']);
                    $sheet->setCellValue('H' . (($i + 1) + 1), $allParcel_data[$i]['sizePrice']);
                    $sheet->setCellValue('I' . (($i + 1) + 1), $allParcel_data[$i]['ffmProduct']);
                    $sheet->setCellValue('J' . (($i + 1) + 1), $allParcel_data[$i]['transactiondate']);
                    $sheet->setCellValue('K' . (($i + 1) + 1), $allParcel_data[$i]['province']);
                }

                // Set Title
                $sheet->setTitle(date("d-m-Y"));

                // Create the excel file in the tmp directory of the system
                $writer = new Xlsx($spreadsheet);
                if ($request->query->get('type') == 'excel') {
                    $fileName = '945report_all_' . date("d-m-Y") . '.xlsx';
                } else {
                    $fileName = '945report_all_' . date("d-m-Y") . '.csv';
                }

                $temp_file = tempnam(sys_get_temp_dir(), $fileName);
                $writer->setPreCalculateFormulas(false);
                $writer->save($temp_file);
                // Return the excel file as an attachment
                return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
            }
        } else {
            return $this->redirect($this->generateUrl('app_login'));
        }
    }

    /**
     * @Route("report_cod_all/download", name="cod_download_file")
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    function cod_downloadFile(Request $request, PcComApprovedRepository $pcComApprovedRepository)
    {
        date_default_timezone_set("Asia/Bangkok");
        $phoneregis = $this->session->get('phoneNo');
        if ($phoneregis != null) {
            $ref = $request->query->get('ref');
            $cod_stat = $pcComApprovedRepository->getDataByMemberIdAndRef($this->session->get('memberId'), $ref);
            $cod_result = [];

            foreach ($cod_stat as $key => $val) {
                $cod_result[] = $cod_stat[$key]['transferAmt'];
                foreach ($val as $k => $v) {
                    if ($k === 'orderphoneno') {
                        $cod_stat[$key]['orderphoneno'] = $this->doubleSix2Zero($v);
                    }
                    if ($k === 'sd') {
                        $cod_stat[$key]['sd'] = $this->formatDateString($v);
                    }
                    if ($k === 'td') {
                        $cod_stat[$key]['td'] = $this->formatDateString($v);
                    }
                    if ($k === 'tfd') {
                        $cod_stat[$key]['tfd'] = $this->formatDate($v);
                    }
                }
            }
            $total = array('total' => array_sum($cod_result));

            $spreadsheet = new Spreadsheet();
            /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
            $sheet = $spreadsheet->getActiveSheet();

            // Set Topic
            $sheet->setCellValue('A1', 'TRACKING');
            $sheet->setCellValue('B1', 'ผู้รับ');
            $sheet->setCellValue('C1', 'ยอด');
            $sheet->setCellValue('D1', '3%');
            $sheet->setCellValue('E1', 'ยอดโอน');
            $sheet->setCellValue('F1', 'ส่ง');
            $sheet->setCellValue('G1', 'ส่งสำเร็จ');
            $sheet->setCellValue('H1', 'โอนสำเร็จ');

            $sheet->getStyle('A1:H1')->getFont()->setBold(true);

            // Set Value
            for ($i = 0; $i < count($cod_stat); $i++) {
                $sheet->setCellValue('A' . (($i + 1) + 1), $cod_stat[$i]['tracking']);
                $sheet->setCellValue('B' . (($i + 1) + 1),
                    $cod_stat[$i]['ordername'] . ' ' . $cod_stat[$i]['orderphoneno']);
                $sheet->setCellValue('C' . (($i + 1) + 1), $cod_stat[$i]['billAmt']);
                $sheet->setCellValue('D' . (($i + 1) + 1), $cod_stat[$i]['codFee']);
                $sheet->setCellValue('E' . (($i + 1) + 1), $cod_stat[$i]['transferAmt']);
                $sheet->setCellValue('F' . (($i + 1) + 1), $cod_stat[$i]['sd']);
                $sheet->setCellValue('G' . (($i + 1) + 1), $cod_stat[$i]['td']);
                $sheet->setCellValue('H' . (($i + 1) + 1), $cod_stat[$i]['tfd']);
            }

            // Set Title
            $sheet->setTitle(date("d-m-Y"));

            // Create the excel file in the tmp directory of the system
            $writer = new Xlsx($spreadsheet);
            if ($request->query->get('type') == 'excel') {
                $fileName = '945report_cod_' . date("d-m-Y") . '.xlsx';
            } else {
                $fileName = '945report_cod_' . date("d-m-Y") . '.csv';
            }

            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($temp_file);
            // Return the excel file as an attachment
            return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            return $this->redirect($this->generateUrl('app_login'));
        }
    }

    function cutSyntaxToWord($word)
    {
        $word = trim($word);
        $word2 = explode("||", $word);
        return $word2[1];
    }

    function changeSyntaxData($word)
    {
        $word = trim($word);
        $word2 = (explode("/", $word));
        return $word2[0];
    }

    function changeSyntaxData2($word)
    {
        $prefix = '=';
        $word = trim($word);
        $word2 = str_replace("<br>", " ", $word);
        if (substr($word2, 0, strlen($prefix)) == $prefix) {
            $word2 = substr($word2, strlen($prefix));
        }
        return $word2;
    }

    public function doubleSix2Zero($phoneNO)
    {
        $pattern = '/^66\d{9}$/';
        $phoneNO = trim($phoneNO);
        if (preg_match($pattern, $phoneNO)) {
            $arr = str_split($phoneNO);
            if (isset($arr[0], $arr[1])) {
                if ($arr[0] . $arr[1] == '66') {
                    $phoneNO = '0';
                    for ($i = 2; $i < count($arr); $i++) {
                        $phoneNO .= $arr[$i];
                    }
                }
            }
        }
        return $phoneNO;
    }

    function formatDate($date)
    {
        if ($date != null) {
            return $date->format('d-m-Y');
        }
    }

    function formatDateString($date)
    {
        if ($date != null) {
            return date("d-m-Y", strtotime($date));
        }
    }

    function changeSyntaxData3($word)
    {
        $word = trim($word);
        $word2 = str_replace("<br>", "\n", $word);
        return $word2;
    }
}
