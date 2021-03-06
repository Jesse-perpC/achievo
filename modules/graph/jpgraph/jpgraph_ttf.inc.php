<?php
//=======================================================================
// File:	jpgraph_ttf.inc.php
// Description:	Handling of TTF fonts
// Created: 	2006-11-19
// Ver:		$Id$
//
// Copyright (c) Aditus Consulting. All rights reserved.
//========================================================================
// TTF Font families
DEFINE("FF_COURIER", 10);
DEFINE("FF_VERDANA", 11);
DEFINE("FF_TIMES", 12);
DEFINE("FF_COMIC", 14);
DEFINE("FF_ARIAL", 15);
DEFINE("FF_GEORGIA", 16);
DEFINE("FF_TREBUCHE", 17);

// Gnome Vera font
// Available from http://www.gnome.org/fonts/
DEFINE("FF_VERA", 18);
DEFINE("FF_VERAMONO", 19);
DEFINE("FF_VERASERIF", 20);

//GNU FreeFont
DEFINE("FF_FREESANS", 25);

// Chinese font
DEFINE("FF_SIMSUN", 30);
DEFINE("FF_CHINESE", 31);
DEFINE("FF_BIG5", 31);

// Japanese font
DEFINE("FF_MINCHO", 40);
DEFINE("FF_PMINCHO", 41);
DEFINE("FF_GOTHIC", 42);
DEFINE("FF_PGOTHIC", 43);

// Hebrew fonts
DEFINE("FF_DAVID", 44);
DEFINE("FF_MIRIAM", 45);
DEFINE("FF_AHRON", 46);

// Extra fonts
// Download fonts from 
// http://www.webfontlist.com
// http://www.webpagepublicity.com/free-fonts.html

DEFINE("FF_SPEEDO", 50);  // This font is also known as Bauer (Used for gauge fascia)
DEFINE("FF_DIGITAL", 51); // Digital readout font
DEFINE("FF_COMPUTER", 52); // The classic computer font
DEFINE("FF_CALCULATOR", 53); // Triad font
// Limits for fonts
DEFINE("_FIRST_FONT", 10);
DEFINE("_LAST_FONT", 53);

// TTF Font styles
DEFINE("FS_NORMAL", 9001);
DEFINE("FS_BOLD", 9002);
DEFINE("FS_ITALIC", 9003);
DEFINE("FS_BOLDIT", 9004);
DEFINE("FS_BOLDITALIC", 9004);

//Definitions for internal font
DEFINE("FF_FONT0", 1);
DEFINE("FF_FONT1", 2);
DEFINE("FF_FONT2", 4);

//=================================================================
// CLASS LanguageConv
// Description: 
// Converts various character encoding into proper
// UTF-8 depending on how the library have been configured and
// what font family is being used
//=================================================================
class LanguageConv
{

    function Convert($aTxt, $aFF)
    {
        return atk_iconv(atkGetCharset(), "utf-8", $aTxt);
    }

}

//=============================================================
// CLASS TTF
// Description: Handle TTF font names and mapping and loading of 
//              font files
//=============================================================
class TTF
{
    private $font_files, $style_names;

//---------------
// CONSTRUCTOR
    function TTF()
    {

        // String names for font styles to be used in error messages
        $this->style_names = array(FS_NORMAL => 'normal',
            FS_BOLD => 'bold',
            FS_ITALIC => 'italic',
            FS_BOLDITALIC => 'bolditalic');

        // File names for available fonts
        $this->font_files = array(
            FF_COURIER => array(FS_NORMAL => 'cour.ttf',
                FS_BOLD => 'courbd.ttf',
                FS_ITALIC => 'couri.ttf',
                FS_BOLDITALIC => 'courbi.ttf'),
            FF_GEORGIA => array(FS_NORMAL => 'georgia.ttf',
                FS_BOLD => 'georgiab.ttf',
                FS_ITALIC => 'georgiai.ttf',
                FS_BOLDITALIC => ''),
            FF_TREBUCHE => array(FS_NORMAL => 'trebuc.ttf',
                FS_BOLD => 'trebucbd.ttf',
                FS_ITALIC => 'trebucit.ttf',
                FS_BOLDITALIC => 'trebucbi.ttf'),
            FF_VERDANA => array(FS_NORMAL => 'verdana.ttf',
                FS_BOLD => 'verdanab.ttf',
                FS_ITALIC => 'verdanai.ttf',
                FS_BOLDITALIC => ''),
            FF_TIMES => array(FS_NORMAL => 'times.ttf',
                FS_BOLD => 'timesbd.ttf',
                FS_ITALIC => 'timesi.ttf',
                FS_BOLDITALIC => 'timesbi.ttf'),
            FF_COMIC => array(FS_NORMAL => 'comic.ttf',
                FS_BOLD => 'comicbd.ttf',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_ARIAL => array(FS_NORMAL => 'arial.ttf',
                FS_BOLD => 'arialbd.ttf',
                FS_ITALIC => 'ariali.ttf',
                FS_BOLDITALIC => 'arialbi.ttf'),
            FF_VERA => array(FS_NORMAL => 'Vera.ttf',
                FS_BOLD => 'VeraBd.ttf',
                FS_ITALIC => 'VeraIt.ttf',
                FS_BOLDITALIC => 'VeraBI.ttf'),
            FF_VERAMONO => array(FS_NORMAL => 'VeraMono.ttf',
                FS_BOLD => 'VeraMoBd.ttf',
                FS_ITALIC => 'VeraMoIt.ttf',
                FS_BOLDITALIC => 'VeraMoBI.ttf'),
            FF_VERASERIF => array(FS_NORMAL => 'VeraSe.ttf',
                FS_BOLD => 'VeraSeBd.ttf',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_FREESANS => array(FS_NORMAL => 'FreeSans.ttf',
                FS_BOLD => 'FreeSansBold.ttf',
                FS_ITALIC => 'FreeSansOblique.ttf',
                FS_BOLDITALIC => 'FreeSansBoldOblique.ttf'),
            /* Chinese fonts */
            FF_SIMSUN => array(FS_NORMAL => 'simsun.ttc',
                FS_BOLD => 'simhei.ttf',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_CHINESE => array(FS_NORMAL => CHINESE_TTF_FONT,
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            /* Japanese fonts */
            FF_MINCHO => array(FS_NORMAL => MINCHO_TTF_FONT,
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_PMINCHO => array(FS_NORMAL => PMINCHO_TTF_FONT,
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_GOTHIC => array(FS_NORMAL => GOTHIC_TTF_FONT,
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_PGOTHIC => array(FS_NORMAL => PGOTHIC_TTF_FONT,
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_MINCHO => array(FS_NORMAL => PMINCHO_TTF_FONT,
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            /* Hebrew fonts */
            FF_DAVID => array(FS_NORMAL => 'DAVIDNEW.TTF',
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_MIRIAM => array(FS_NORMAL => 'MRIAMY.TTF',
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_AHRON => array(FS_NORMAL => 'ahronbd.ttf',
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            /* Misc fonts */
            FF_DIGITAL => array(FS_NORMAL => 'DIGIRU__.TTF',
                FS_BOLD => 'Digirtu_.ttf',
                FS_ITALIC => 'Digir___.ttf',
                FS_BOLDITALIC => 'DIGIRT__.TTF'),
            FF_SPEEDO => array(FS_NORMAL => 'Speedo.ttf',
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_COMPUTER => array(FS_NORMAL => 'COMPUTER.TTF',
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
            FF_CALCULATOR => array(FS_NORMAL => 'Triad_xs.ttf',
                FS_BOLD => '',
                FS_ITALIC => '',
                FS_BOLDITALIC => ''),
        );
    }

//---------------
// PUBLIC METHODS	
    // Create the TTF file from the font specification
    function File($family, $style = FS_NORMAL)
    {
        $fam = @$this->font_files[$family];
        if (!$fam) {
            JpGraphError::RaiseL(25046, $family); //("Specified TTF font family (id=$family) is unknown or does not exist. Please note that TTF fonts are not distributed with JpGraph for copyright reasons. You can find the MS TTF WEB-fonts (arial, courier etc) for download at http://corefonts.sourceforge.net/");
        }
        $f = @$fam[$style];

        if ($f === '')
            JpGraphError::RaiseL(25047, $this->style_names[$style], $this->font_files[$family][FS_NORMAL]); //('Style "'.$this->style_names[$style].'" is not available for font family '.$this->font_files[$family][FS_NORMAL].'.');
        if (!$f) {
            JpGraphError::RaiseL(25048, $fam); //("Unknown font style specification [$fam].");
        }

        if ($family >= FF_MINCHO && $family <= FF_PGOTHIC) {
            $f = MBTTF_DIR . $f;
        } else {
            $f = TTF_DIR . $f;
        }

        if (file_exists($f) === false || is_readable($f) === false) {
            JpGraphError::RaiseL(25049, $f); //("Font file \"$f\" is not readable or does not exist.");
        }
        return $f;
    }

}

// Class
?>
