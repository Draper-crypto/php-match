<?php
declare (strict_types=1);

namespace addons\excel;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Excelclass
{

    /**
     * 导出excel
     * @param array $data   数据
     * @param array $title  第一行 标题列表
     * @return bool|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportExcel($data = array(), $title = array(), $type = "")
    {
        try {
            if (!$data && $type != "dome") {
                return false;
            }
            set_time_limit(0);
            $spreadsheet = new Spreadsheet();
            /* 设置默认文字居左，上下居中 */
            $styleArray = [
                'alignment' => [
                    'horizontal' => 'left',
                    'vertical' => 'center',
                ],
            ];

            $spreadsheet->getDefaultStyle()->applyFromArray($styleArray);
            $worksheet = $spreadsheet->getActiveSheet();
            //设置工作表标题名称
            //$worksheet->setTitle('工作表格1');
            $i = 0;
            //表头 设置单元格内容
            foreach ($title as $key => $value) {
                $worksheet->setCellValueByColumnAndRow($i + 1, 1, $value['name']);
                if ($type == "dome") {
                    if ($value['type'] == 'enum') {
                        $maxRows = 100;//模板填充行数
                        foreach (range(2, $maxRows) as $row) {
                            $this->setValidation($worksheet, $this->IntToChr($i).$row, '"' . $value['typeval'] . '"');
                        }
                    }
                    if ($key == "create_time" || $key == "update_time" || $key == "publish_time") {
                        $maxRows = 100;//模板填充行数
                        foreach (range(2, $maxRows) as $row) {
                            $worksheet->getStyle($this->IntToChr($i).$row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2);
                        }
                    }
                    if ($key == 'lang') {
                        $maxRows = 100;//模板填充行数
                        foreach (range(2, $maxRows) as $row) {
                            $this->setValidation($worksheet, $this->IntToChr($i).$row, '"' . implode(',',array_keys(site()['content_lang_list'])) . '"');
                        }
                    }
                }
                $worksheet->getColumnDimension($this->IntToChr($i))->setWidth(20);
                $i++;
            }

            if ($type == "dome") {

                $fileType = 'Xlsx';
                $writer = IOFactory::createWriter($spreadsheet, $fileType);
                $this->excelBrowserExport(date('YmdHis', time()), $fileType);
                $writer->save('php://output');
            } else {
                if (empty($title)) {
                    $row = 1;
                } else {
                    $row = 2;
                }
                foreach ($data as $item) {
                    $column = 1;
                    foreach ($item as $k => $value) {
                        if ($k == "create_time" || $k == "update_time" || ($k == "publish_time" && $title[$k]['type'] == 'int')) {
                            $worksheet->setCellValueByColumnAndRow($column, $row, date('Y-m-d H:i:s',$value));
                        } else if($k == 'thumb'){
                            $file_path = public_path().$value;
                            if (!empty($value) && file_exists($file_path)) {
                                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                $drawing->setName($value);

                                $drawing->setDescription($value);

                                $drawing->setPath($file_path);

                                $drawing->setHeight(36);

                                //$drawing->setWidth(50);
                                $drawing->setCoordinates($this->IntToChr($column-1).$row);

                                $drawing->setWorksheet($worksheet);
                                //设置行高
                                $worksheet->getRowDimension($row)->setRowHeight(30);
                            } else {
                                $worksheet->setCellValueByColumnAndRow($column, $row, $this->str($value));
                            }
                        } else {
                            $worksheet->setCellValueByColumnAndRow($column, $row, $this->str($value));
                        }
                        $column++;
                    }
                    $row++;
                }
                $worksheet->getStyle("B1:".$worksheet->getHighestColumn().$worksheet->getHighestRow())->getAlignment()->setWrapText(true);
                //保存文件路径
                if ($type == "dome") {
                    $path = 'uploads' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . 'dome' . DIRECTORY_SEPARATOR;
                } else {
                    $path = 'uploads' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
                }
                $fileType = 'Xlsx';
                $writer = IOFactory::createWriter($spreadsheet, $fileType);
                if ($fileType == 'Excel2007' || $fileType == 'Xlsx') {
                    $fileName = date('YmdHis', time()).".xlsx";
                } else {
                    $fileName = date('YmdHis', time()).".xls";
                }
                //创建目录
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
                $writer->save($path.$fileName);
            }
            /* 释放内存 */
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            ob_end_flush();
            if ($type == "dome") {
                return true;
            } else {
                return $path . $fileName;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 导入Excel
     * @param string $file
     * @param int $sheet
     * @return mixed
     * @throws \Exception
     */
    public function importExcel($file = '', $sheet = 0)
    {
        try {
            /* 转码 */
            $file = iconv("utf-8", "gb2312", $file);
            if (empty($file) OR !file_exists($file)) {
                throw new \Exception('文件不存在!');
            }
            /** @var Xlsx $objRead */
            $objRead = IOFactory::createReader('Xlsx');
            if (!$objRead->canRead($file)) {
                /** @var Xls $objRead */
                $objRead = IOFactory::createReader('Xls');
                if (!$objRead->canRead($file)) {
                    throw new \Exception('只支持导入Excel文件！');
                }
            }
            /* 建立excel对象 */
            $obj = $objRead->load($file);
            /* 获取指定的sheet表 */
            $currSheet = $obj->getSheet($sheet);
            $data = $currSheet->toArray();
            $imageFilePath = 'uploads' . DIRECTORY_SEPARATOR . 'myexcel' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
            if (!file_exists($imageFilePath)) { //如果目录不存在则递归创建
                mkdir($imageFilePath, 0755, true);
            }
            foreach ($currSheet->getDrawingCollection() as $drawing) {
                list($startColumn, $startRow) = Coordinate::coordinateFromString($drawing->getCoordinates());
                $imageFileName = $drawing->getCoordinates() . mt_rand(1000, 9999);
                $imageType = $drawing->getExtension();
                $imageSaveName = $imageFilePath . $imageFileName . '.' . $imageType;
                $sourcePath = $drawing->getPath();
                $this->imageMerge($sourcePath, $imageSaveName, $imageType);
                $startColumn = $this->ABC2decimal($startColumn);
                $data[$startRow - 1][$startColumn] = DIRECTORY_SEPARATOR . $imageSaveName;
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //当前列
    public function ABC2decimal($abc)
    {
        $ten = 0;
        $len = strlen($abc);
        for ($i = 1; $i <= $len; $i++) {
            $char = substr($abc, 0 - $i, 1);//反向获取单个字符
            $int = ord($char);
            $ten += ($int - 65) * pow(26, $i - 1);
        }
        return $ten;
    }

    //图片处理
    public function imageMerge($o_pic = '', $out_pic = '', $type_pic = 'png')
    {
        if (!$o_pic && !$out_pic) {
            return false;
        }
        // 获取原图像信息 宽高
        list($src_w, $src_h) = getimagesize($o_pic);
        if ('jpg' == $type_pic || 'jpeg' == $type_pic) {
            //读取jpeg/jpg图片
            $src_im = imagecreatefromjpeg($o_pic);
        } elseif ('png' == $type_pic) {
            //读取png图片
            $src_im = imagecreatefrompng($o_pic);
        } elseif ('gif' == $type_pic) {
            //读取gif图片
            $src_im = imagecreatefromgif($o_pic);
        } else {
            return false;
        }
        // 背景图片
        $background = imagecreatetruecolor($src_w, $src_h);
        // 为真彩色画布创建白色背景，再设置为透明
        $color = imagecolorallocate($background, 202, 201, 201);
        // 填充颜色
        imagefill($background, 0, 0, $color);
        // 设置透明
        imageColorTransparent($background, $color);
        // 最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度
        imagecopyresized($background, $src_im, 0, 0, 0, 0, $src_w, $src_h, $src_w, $src_h);
        if ('jpg' == $type_pic || 'jpeg' == $type_pic) {
            imagejpeg($background, $out_pic);
        } elseif ('png' == $type_pic) {
            header('Content-Type: image/png');
            imagepng($background, $out_pic);
        } elseif ('gif' == $type_pic) {
            imagegif($background, $out_pic);
        } else {
            return false;
        }
        imagedestroy($src_im);
        imagedestroy($background);
    }

    private function excelBrowserExport($fileName = '', $fileType = '')
    {
        //文件名称校验
        if (!$fileName) {
            throw new Exception('文件名不能为空');
        }
        //Excel文件类型校验
        $type = ['Excel2007', 'Xlsx', 'Excel5', 'xls'];
        if (!in_array($fileType, $type)) {
            throw new Exception('未知文件类型');
        }
        if ($fileType == 'Excel2007' || $fileType == 'Xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
            header('Cache-Control: max-age=0');
        }
    }

    /**
     * 设置某个单元格的下拉列表规则
     * @param Worksheet $sheet
     * @param [string] $cellPoint 单元格坐标. A1
     * @param [sting] $format 公式
     * @return void
     */
    protected function setValidation(Worksheet $sheet, $cellPoint, $format)
    {
        $validation = $sheet -> getCell($cellPoint) -> getDataValidation();
        $validation -> setType(DataValidation::TYPE_LIST);
        $validation -> setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation -> setAllowBlank(false);
        $validation -> setShowInputMessage(true);
        $validation -> setShowErrorMessage(true);
        $validation -> setShowDropDown(true);
        $validation -> setErrorTitle('输出错误');
        $validation -> setError('值不在列表中');
        $validation -> setPromptTitle('');
        $validation -> setPrompt('请从列表中选择一个值');
        $validation -> setFormula1($format);
    }

    /**
     * 数字转字母
     * @param Int $index 索引值
     * @param Int $start 字母起始值
     * @return String 返回字母
     */
    function IntToChr($index, $start = 65) {
        $str = '';
        if (floor($index / 26) > 0) {
            $str .= $this->IntToChr(floor($index / 26)-1);
        }
        return $str . chr($index % 26 + $start);
    }

    //清除HTML标签
    function str($str = '')
    {
        if ($str == '' || !is_string($str)) {
            return $str;
        }
        return htmlspecialchars_decode($str);
    }
}