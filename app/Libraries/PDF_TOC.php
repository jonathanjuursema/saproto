<?php

namespace App\Libraries;

class PDF_TOC extends AlphaPDF
{
    protected $_toc = [];

    protected $_numbering = false;

    protected $_numberingFooter = false;

    protected $_numPageNum = 1;

    public function AddPage($orientation = '', $size = '', $rotation = 0): void
    {
        parent::AddPage($orientation, $size, $rotation);
        if ($this->_numbering) {
            $this->_numPageNum++;
        }
    }

    public function startPageNums(): void
    {
        $this->_numbering = true;
        $this->_numberingFooter = true;
    }

    public function stopPageNums(): void
    {
        $this->_numbering = false;
    }

    public function numPageNo()
    {
        return $this->_numPageNum;
    }

    public function TOC_Entry($txt, $level = 0): void
    {
        $this->_toc[] = ['t' => $txt, 'l' => $level, 'p' => $this->numPageNo()];
    }

    public function insertTOC($location = 1,
        $labelSize = 20,
        $entrySize = 10,
        $tocfont = 'Times',
        $label = 'Table of Contents'
    ): void {
        // make toc at end
        $this->stopPageNums();
        $this->AddPage();
        $tocstart = $this->page;

        $this->SetFont($tocfont, 'B', $labelSize);
        $this->Cell(0, 1, $label, 0, 1, 'L');
        $this->Ln(1);

        foreach ($this->_toc as $t) {

            // Offset
            $level = $t['l'];
            if ($level > 0) {
                $this->Cell($level * 8);
            }

            $weight = '';
            if ($level == 0) {
                $weight = 'B';
                $this->Ln(1);
            }

            $str = $t['t'];
            $this->SetFont($tocfont, $weight, $entrySize);
            $strsize = $this->GetStringWidth($str);
            $this->Cell($strsize + 2, $this->FontSize + 2, $str);

            // Filling dots
            $this->SetFont($tocfont, '', $entrySize);
            $PageCellSize = $this->GetStringWidth($t['p']) + 2;
            $w = $this->w - $this->lMargin - $this->rMargin - $PageCellSize - ($level * 8) - ($strsize + 2);
            $this->Cell($w, $this->FontSize + 2, '', 0, 0, 'R');

            // Page number
            $this->Cell($PageCellSize, $this->FontSize, $level == 0 ? '' : $t['p'], 0, 1, 'R');
        }

        // Grab it and move to selected location
        $n = $this->page;
        $n_toc = $n - $tocstart + 1;
        $last = [];

        // store toc pages
        for ($i = $tocstart; $i <= $n; $i++) {
            $last[] = $this->pages[$i];
        }

        // move pages
        for ($i = $tocstart - 1; $i >= $location - 1; $i--) {
            $this->pages[$i + $n_toc] = $this->pages[$i];
        }

        // Put toc pages at insert point
        for ($i = 0; $i < $n_toc; $i++) {
            $this->pages[$location + $i] = $last[$i];
        }
    }

    public function Footer(): void
    {
        if (! $this->_numberingFooter) {
            return;
        }

        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 7, $this->numPageNo(), 0, 0, 'R');
        if (! $this->_numbering) {
            $this->_numberingFooter = false;
        }
    }
}
