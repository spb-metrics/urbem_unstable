<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Carnê IPTU 2008 para Mata de Sao Joao
  * Data de criação : 29/11/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @package URBEM

  Caso de uso: uc-05.03.11
*/

include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebrabanCompensacaoBB-Anexo5.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';

define       ('FPDF_FONTPATH','font/');

class RProtocolo extends fpdf
{
    /* Labels  */
    public $Titulo1         = 'P M MATA DE SÃO JOÃO';
    public $Titulo2         = '';
    public $lblContribuinte = 'CONTRIBUINTE';
    public $lblInscricao    = 'INSCRIÇÃO IMOB.';
    public $lblCtmDci       = 'CTM/DCI';
    public $lblLogradouro   = 'CÓDIGO LOGRADOURO';
    public $lblDistrito     = 'DIST.';
    public $lblCt           = 'CT';
    public $lblCa           = 'CA';
    public $lblDtProcesso   = 'DATA PROCESSAMENTO';
    public $lblLocalizacao  = 'LOCALIZAÇÃO DO IMÓVEL';
    public $lblTerreno      = 'ÁREA DO TERRENO';
    public $lblEdificada    = 'ÁREA EDIFICADA';
    public $lblUtilizacaoIm = 'UTILIZAÇÃO DO IMÓVEL';
    public $lblTributo      = 'TRIBUTO';
    public $lblNroPlanta    = 'Nº DA PLANTA';
    public $lblExercicio    = 'EXERCÍCIO';
    public $lblVlTribReal   = 'VALOR TRIBUTÁVEL - REAL';
    public $lblImpAnualReal = 'IMPOSTO - REAL';
    public $lblObservacao   = 'OBSERVAÇÃO';
    public $lblLimpAnualRl  = 'TX. COLETA LIXO ANUAL - REAL';
    public $lblTotalAnualRl = 'TOTAL ANUAL - REAL';
    public $lblReferencia   = 'REFERÊNCIA';
    public $lblTxAverbacao  = 'ALIQUOTA';
    public $lblUrbem      = 'URBEM - Soluções Integradas de Administração Municipal - www.urbem.cnm.org.br';
    public $lblTotalLancado = 'TOTAL LANÇADO';

    /* Variaveis */
    public $Imagem;
    public $stNomCgm;
    public $stRua;
    public $stNumero;
    public $stComplemento;
    public $stCidade;
    public $stUf;
    public $stCep;
    public $inInscricao;
    public $inCtmDci;
    public $inCodLogradouro;
    public $inDistrito;
    public $inCt;
    public $inCa;
    public $dtProcessamento;
    public $flAreaTerreno;
    public $flAreaEdificada;
    public $stUtilizacaoImovel;
    public $stTributo;
    public $stTributo2;
    public $stTributo3;
    public $flValorTributoReal;
    public $flImpostoAnualReal;
    public $flTotalAnualReal;
    public $stObservacao;
    public $flTaxaLimpezaAnual;
    public $inReferencia;
    public $inNumeroPlanta;
    public $stQuadro1;
    public $stQuadro2;
    public $stQuadro3;
    public $stExercicio;
    public $stNomBairro;
    public $stLoginUsuario;
    public $stCodUsuario;
    public $stEnderecoEntrega;
    public $flTxAverbacao;
    public $flTotalLancado;
    public $inTamY = 30;
    public $stAdquirente;

    /* setters */
    public function setImagem($valor) { $this->Imagem             = $valor; }
    public function setNomCgm($valor) { $this->stNomCgm           = $valor; }
    public function setRua($valor) { $this->stRua              = $valor; }
    public function setNumero($valor) { $this->stNumero           = $valor; }
    public function setComplemento($valor) { $this->stComplemento      = $valor; }
    public function setCidade($valor) { $this->stCidade           = $valor; }
    public function setUf($valor) { $this->stUf               = $valor; }
    public function setCep($valor) { $this->stCep              = $valor; }
    public function setInscricao($valor) { $this->inInscricao        = $valor; }
    public function setCtmDci($valor) { $this->inCtmDci           = $valor; }
    public function setCodLogradouro($valor) { $this->inCodLogradouro    = $valor; }
    public function setDistrito($valor) { $this->inDistrito         = $valor; }
    public function setCt($valor) { $this->inCt               = $valor; }
    public function setCa($valor) { $this->inCa               = $valor; }
    public function setProcessamento($valor) { $this->dtProcessamento    = $valor; }
    public function setAreaTerreno($valor) { $this->flAreaTerreno      = $valor; }
    public function setAreaEdificada($valor) { $this->flAreaEdificada    = $valor; }
    public function setUtilizacaoImovel($valor) { $this->stUtilizacaoImovel = $valor; }
    public function setTributo($valor) { $this->stTributo          = $valor; }
    public function setTributo2($valor) { $this->stTributo2         = $valor; }
    public function setTributo3($valor) { $this->stTributo3         = $valor; }
    public function setValorTributoReal($valor) { $this->flValorTributoReal = $valor; }
    public function setImpostoAnualReal($valor) { $this->flImpostoAnualReal = $valor; }
    public function setObservacao($valor) { $this->stObservacao       = $valor; }
    public function setTaxaLimpezaAnual($valor) { $this->flTaxaLimpezaAnual = $valor; }
    public function setValorAnualReal($valor) { $this->flValorAnualReal   = $valor; }
    public function setReferencia($valor) { $this->inReferencia       = $valor; }
    public function setNumeroPlanta($valor) { $this->inNumeroPlanta     = $valor; }
    public function setQuadro1($valor) { $this->stQuadro1          = $valor; }
    public function setQuadro2($valor) { $this->stQuadro2          = $valor; }
    public function setQuadro3($valor) { $this->stQuadro3          = $valor; }
    public function setExercicio($valor) { $this->stExercicio        = $valor; }
    public function setNomBairro($valor) { $this->stNomBairro        = $valor; }
    public function setLoginUsuario($valor) { $this->stLoginUsuario     = $valor; }
    public function setCodUsuario($valor) { $this->stCodUsuario       = $valor; }
    public function setEndEntrega($valor) { $this->stEnderecoEntrega  = $valor; }
    public function setTotalLancado($valor) { $this->flTotalLancado     = $valor; }

    /* getters */
    public function getImagem() { return $this->Imagem             ; }
    public function getNomCgm() { return $this->stNomCgm           ; }
    public function getRua() { return $this->stRua              ; }
    public function getNumero() { return $this->stNumero           ; }
    public function getComplemento() { return $this->stComplemento      ; }
    public function getCidade() { return $this->stCidade           ; }
    public function getUf() { return $this->stUf               ; }
    public function getCep() { return $this->stCep              ; }
    public function getInscricao() { return $this->inInscricao        ; }
    public function getCtmDci() { return $this->inCtmDci           ; }
    public function getCodLogradouro() { return $this->inCodLogradouro    ; }
    public function getDistrito() { return $this->inDistrito         ; }
    public function getCt() { return $this->inCt               ; }
    public function getCa() { return $this->inCa               ; }
    public function getProcessamento() { return $this->dtProcessamento    ; }
    public function getAreaTerreno() { return $this->flAreaTerreno      ; }
    public function getAreaEdificada() { return $this->flAreaEdificada    ; }
    public function getUtilizacaoImovel() { return $this->stUtilizacaoImovel ; }
    public function getTributo() { return $this->stTributo          ; }
    public function getValorTributoReal() { return $this->flValorTributoReal ; }
    public function getImpostoAnualReal() { return $this->flImpostoAnualReal ; }
    public function getObservacao() { return $this->stObservacao       ; }
    public function getTaxaLimpezaAnual() { return $this->flTaxaLimpezaAnual ; }
    public function getValorAnualReal() { return $this->flValorAnualReal   ; }
    public function getReferencia() { return $this->inReferencia       ; }
    public function getNumeroPlanta() { return $this->inNumeroPlanta     ; }
    public function getQuadro1() { return $this->stQuadro1          ; }
    public function getQuadro2() { return $this->stQuadro2          ; }
    public function getQuadro3() { return $this->stQuadro3          ; }
    public function getExercicio() { return $this->stExercicio        ; }
    public function getNomBairro() { return $this->stNomBairro        ; }
    public function getLoginUsuario() { return $this->stLoginUsuario     ; }
    public function getCodUsuario() { return $this->stCodUsuario       ; }
    public function getEndEntrega() { return $this->stEnderecoEntrega  ; }
    public function getTotalLancado() { return $this->flTotalLancado     ; }

    public function defineCodigoBarras($xpos, $ypos, $code, $basewidth = 0.7, $height = 10)
    {
        //global $pdf;
        $wide = $basewidth;
        $narrow = $basewidth / 2 ;

        // wide/narrow codes for the digits
        $barChar['0'] = 'nnwwn';
        $barChar['1'] = 'wnnnw';
        $barChar['2'] = 'nwnnw';
        $barChar['3'] = 'wwnnn';
        $barChar['4'] = 'nnwnw';
        $barChar['5'] = 'wnwnn';
        $barChar['6'] = 'nwwnn';
        $barChar['7'] = 'nnnww';
        $barChar['8'] = 'wnnwn';
        $barChar['9'] = 'nwnwn';
        $barChar['A'] = 'nn';
        $barChar['Z'] = 'wn';

        // add leading zero if code-length is odd
        if (strlen($code) % 2 != 0) {
            $code = '0' . $code;
        }

        $this->SetFont('Arial','',10);
        $this->SetFillColor(0);

        // add start and stop codes
        $code = 'AA'.strtolower($code).'ZA';

        for ($i=0; $i<strlen($code); $i=$i+2) {
            // choose next pair of digits
            $charBar = $code{$i};
            $charSpace = $code{$i+1};
            // check whether it is a valid digit
            if (!isset($barChar[$charBar])) {
                $this->Error('Invalid character in barcode: '.$charBar);
            }
            if (!isset($barChar[$charSpace])) {
                $this->Error('Invalid character in barcode: '.$charSpace);
            }
            // create a wide/narrow-sequence (first digit=bars, second digit=spaces)
            $seq = '';
            for ($s=0; $s<strlen($barChar[$charBar]); $s++) {
                $seq .= $barChar[$charBar]{$s} . $barChar[$charSpace]{$s};
            }
            for ($bar=0; $bar<strlen($seq); $bar++) {
                // set lineWidth depending on value
                if ($seq{$bar} == 'n') {
                    $lineWidth = $narrow;
                } else {
                    $lineWidth = $wide;
                }
                // draw every second value, because the second digit of the pair is represented by the spaces
                if ($bar % 2 == 0) {
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
        }
    }

    /* configura a pagina de carne */
    public function configuraProtocolo()
    {
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
    }

    /* layout do protocolo */
    public function drawProtocolo()
    {
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->lblVlTribReal   = 'VALOR TRIBUTÁVEL';
            //$this->lblImpAnualReal = 'IMPOSTO ANUAL';
            $this->lblImpAnualReal = 'IMPOSTO - REAL';
            //$this->lblTotalAnualRl = 'TOTAL ANUAL';
            $this->lblTotalAnualRl = 'TOTAL - REAL';

        }

        $this->setLoginUsuario ( Sessao::read( 'nomCgm' ) );
        $this->setCodUsuario ( Sessao::read('numCgm') );

        $this->setFont('Arial','',10);

        //thisfineCodigoBarras(10,50,'816100000016143532732003601232006101100000000016');

        /* retangula mais externo */
        $this->Rect( 7, 8+$this->inTamY, 196, 82);

        /* linhas horizontais maiores */
        $this->Line( 7, 27+$this->inTamY, 203, 27+$this->inTamY );
        $this->Line( 7, 47+$this->inTamY, 203, 47+$this->inTamY );
        $this->Line( 7, 62+$this->inTamY, 203, 62+$this->inTamY );
        $this->Line( 7, 71+$this->inTamY, 203, 71+$this->inTamY );

        /* lisinhas horizontais menores */
        $this->Line( 115, 33.65+$this->inTamY, 203, 33.65+$this->inTamY  );
        $this->Line( 115, 40.3+$this->inTamY , 203, 40.3+$this->inTamY   );
        $this->Line( 115, 54.5+$this->inTamY, 203, 54.5+$this->inTamY );
        $this->Line( 115, 80+$this->inTamY, 203, 80+$this->inTamY );

        /* lisinhas verticais */
        $this->Line( 33 , 62+$this->inTamY , 33 , 71+$this->inTamY );
        $this->Line( 58 , 62+$this->inTamY , 58 , 71+$this->inTamY );
        $this->Line( 85 , 62+$this->inTamY , 85 , 71+$this->inTamY );
        $this->Line( 115, 27+$this->inTamY , 115, 90+$this->inTamY );
        $this->Line( 162, 40.3+$this->inTamY , 162, 90+$this->inTamY );
        $this->Line( 152, 40.3+$this->inTamY , 152, 47+$this->inTamY );
        $this->Line( 142, 40.3+$this->inTamY , 142, 47+$this->inTamY );
        $this->Line( 172, 40.3+$this->inTamY , 172, 47+$this->inTamY );

        /* brazao */
        if ($this->Imagem) {
            $stExt = substr( $this->Imagem, strlen($this->Imagem)-3, strlen($this->Imagem) );
            $this->Image( $this->Imagem, 8, 9+$this->inTamY, 25, 16.5, $stExt );
        }

        /* labels fixos */
        $this->setFont('Arial','B',13);     // fonte do primeiro titulo
        $this->Text   ( 60, 17+$this->inTamY, $this->Titulo1 );

        $this->setFont('Arial','B',11);     // fonte do titulo menor
        $this->Text   ( 65, 22+$this->inTamY, $this->Titulo2 );

        $this->setFont('Arial','B',6);       // fonte dos labels dos dados
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 8    , 29.5+$this->inTamY, 'ADQUIRENTE'      );
            $this->setFont('Arial','B',10);
            $this->Text   ( 95   , 26+$this->inTamY  , 'ITIV'      );
            $this->setFont('Arial','B',6);
        } else {
            $this->Text   ( 8    , 29.5, $this->lblContribuinte  );
        }
        $this->Text   ( 115.5, 29.5+$this->inTamY, $this->lblInscricao     );
        $this->Text   ( 115.5, 36+$this->inTamY  , $this->lblCtmDci        );
        $this->Text   ( 115.5, 42.5+$this->inTamY, $this->lblLogradouro    );
        $this->Text   ( 143  , 42.5+$this->inTamY, $this->lblDistrito      );
        $this->Text   ( 153  , 42.5+$this->inTamY, $this->lblCt            );
        $this->Text   ( 163  , 42.5+$this->inTamY, $this->lblCa            );
        $this->Text   ( 173  , 42.5+$this->inTamY, $this->lblDtProcesso    );

        $this->Text   ( 8    , 49.5+$this->inTamY, $this->lblLocalizacao   );
        $this->Text   ( 115.5, 49.5+$this->inTamY, $this->lblTerreno       );
        $this->Text   ( 163  , 49.5+$this->inTamY, $this->lblEdificada     );
        $this->Text   ( 115.5, 57+$this->inTamY  , $this->lblUtilizacaoIm  );
        $this->Text   ( 163  , 57+$this->inTamY  , $this->lblTributo       );

        $this->Text   ( 8    , 64+$this->inTamY  , $this->lblNroPlanta     );
        $this->Text   ( 86   , 64+$this->inTamY  , $this->lblExercicio     );
        $this->Text   ( 115.5, 64+$this->inTamY  , $this->lblVlTribReal    );
        $this->Text   ( 163  , 64+$this->inTamY  , $this->lblImpAnualReal  );

        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 8    , 73+$this->inTamY  ,"REQUERIMENTO DE I.T.I.V." );
        } else {
            $this->Text   ( 8    , 73  , $this->lblObservacao    );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 115.5, 73+$this->inTamY  , "TAXA DE EXPEDIENTE"    );
        } else {
            $this->Text   ( 115.5, 73  , $this->lblLimpAnualRl   );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 163  , 73+$this->inTamY  , $this->lblTxAverbacao );
            $this->Text   ( 163  , 82+$this->inTamY  , $this->lblTotalAnualRl  );
        } else {
            $this->Text   ( 163  , 73  , $this->lblTotalAnualRl  );
            $this->Text   ( 163  , 82  , $this->lblTotalLancado  );
        }

        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            //$this->Text   ( 115.5, 82+$this->inTamY  , 'MULTA DE MORA'    );
        } else {
            $this->Text   ( 115.5, 82  , $this->lblReferencia    );
        }

        $this->setFont('Arial',''  , 5                 );
        $this->Text   ( 8    , 92+$this->inTamY  , $this->lblUrbem       );

        if ($this->stLoginUsuario != "" && $this->stCodUsuario != "") {
            $this->Text   ( 115.5, 92+$this->inTamY  , $this->stCodUsuario." - ".$this->stLoginUsuario );
        }

        /* Fim do layout do quadrado superior */
    }

    /* Posicionamento das variáveis */
    public function posicionaVariaveisProtocolo()
    {
        $this->setFont('Arial', 'b', 7 );
        if ( !Sessao::read( 'itbi_observacao' ) ) {
            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($this->stNomCgm) );
        } else {
            require_once(CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php");
            $obImovelVVenal = new TARRImovelVVenal;
            $filtro = "and inscricao_municipal = ".$this->inInscricao;
            $obImovelVVenal->recuperaMensagemItbi($rsItbi,$filtro);
            $this->stAdquirente = $rsItbi->getCampo('adquirinte');

            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($this->stAdquirente) );
        }
        // array com dados do endereço;
        $arEnd = explode('|*|',$this->stEnderecoEntrega);
        $this->Text   ( 8     , 37.5+$this->inTamY , strtoupper($arEnd[0].' '.$arEnd[1]));
        $this->Text   ( 8     , 41+$this->inTamY   , strtoupper($arEnd[2]." - ".$arEnd[3]) );
        $this->Text   ( 8     , 44.5+$this->inTamY , strtoupper($arEnd[6].' '.$arEnd[5].' CEP:'.$arEnd[4]) );

        $this->Text   ( 156   , 32+$this->inTamY   , strtoupper($this->inInscricao) );
        $this->Text   ( 145   , 37.5+$this->inTamY , strtoupper($this->inCtmDci) );
        $this->Text   ( 122   , 46+$this->inTamY   , strtoupper($this->inCodLogradouro) );
        $this->Text   ( 145   , 46+$this->inTamY   , strtoupper($this->inDistrito)  );
        $this->Text   ( 155   , 46+$this->inTamY   , strtoupper($this->inCt)    );
        $this->Text   ( 165   , 46+$this->inTamY   , strtoupper($this->inCa)    );
        $this->Text   ( 180   , 46+$this->inTamY   , strtoupper($this->dtProcessamento) );

        $this->Text   ( 8     , 54+$this->inTamY   , strtoupper($this->stRua.' '.$this->stNumero) );
        $this->Text   ( 8     , 58+$this->inTamY   , strtoupper($this->stComplemento." - ".$this->stNomBairro) );
        $this->Text   ( 145   , 52.5+$this->inTamY , strtoupper($this->flAreaTerreno) );
        $this->Text   ( 185   , 52.5+$this->inTamY , strtoupper($this->flAreaEdificada) );

        $this->Text   ( 130   , 60.5+$this->inTamY , strtoupper($this->stUtilizacaoImovel) );
        $this->setFont('Arial', 'b'  , 6 );
        $this->Text   ( 165   , 60.5+$this->inTamY , strtoupper(substr($this->stTributo,0,28)) );
        $this->Text   ( 165   , 68.5+$this->inTamY , strtoupper(substr($this->stTributo2,0,28)) );
        $this->setFont('Arial', 'b', 7 );
        $this->Text   ( 90    , 68+$this->inTamY   , strtoupper($this->stExercicio)   );
//        $this->Text   ( 8     , 76.5 , strtoupper($this->stObservacao));

        // Observacao
        // 3 linhas de observacao
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            require_once(CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php");
            $obImovelVVenal = new TARRImovelVVenal;
            $filtro = "and inscricao_municipal = ".$this->inInscricao;
            $obImovelVVenal->recuperaMensagemItbi($rsItbi,$filtro);
            $rsItbi->addFormatacao ('base_calculo','NUMERIC_BR');
            $rsItbi->addFormatacao ('valor_financiado','NUMERIC_BR');
            $rsItbi->addFormatacao ('valor_pactuado','NUMERIC_BR');
            //$stObsL1 =    "Adquirinte       : ".$rsItbi->getCampo('adquirinte');
            //$stObsL2 =    "Transmitente     : ".$rsItbi->getCampo('transmitente');
            $stObsL3 =    "Base de Calculo  : ".$rsItbi->getCampo('base_calculo')."      ITIV: ".$this->flImpostoAnualReal;
            $stObsL4 =    "Valor Financiado : ".$rsItbi->getCampo('valor_financiado');
            //$stObsL5 =    "Valor Pactuado   : ".$rsItbi->getCampo('valor_pactuado');
            $stObsL5 =    "Natureza de Transferência: ".$rsItbi->getCampo('cod_natureza')." - ".$rsItbi->getCampo('descricao');
            if ( $rsItbi->getCampo('cod_processo') )
                $stObsL7 =    "Processo       : ".$rsItbi->getCampo('cod_processo')."/".$rsItbi->getCampo('exercicio');

            $this->Text   ( 8     , 75.5+$this->inTamY , $stObsL3 );
            $this->Text   ( 8     , 78+$this->inTamY   , $stObsL4 );
            $this->Text   ( 8     , 80.5+$this->inTamY , $stObsL5 );
            $this->Text   ( 8     , 83+$this->inTamY   , $stObsL6 );
            $this->Text   ( 8     , 85.5 +$this->inTamY, $stObsL7 );
//            $this->Text   ( 8     , 88   , $stObsL6 );
//            $this->Text   ( 70    , 73   , $stObsL7 );
            // coloca atributo TRANSMITENTE no cabeçaho
            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($rsItbi->getCampo('transmitente') ));

        } else {
            $stObs = str_replace("\n"," ",$this->stObservacao);
            $this->Text   ( 8     , 76.5+$this->inTamY , substr($stObs,0      ,70 ));
            $this->Text   ( 8     , 79+$this->inTamY   , substr($stObs,70     ,70 ));
            $this->Text   ( 8     , 81.5+$this->inTamY , substr($stObs,140    ,70 ));
            $this->Text   ( 8     , 84+$this->inTamY   , substr($stObs,210    ,70 ));
            $this->Text   ( 8     , 86.5+$this->inTamY , substr($stObs,280    ,70 ));
        }
        // caso seja itbi, mostra o valor de base da calculo como valor tributavel
        if ( Sessao::read( 'itbi_observacao' ) == 'sim')
            $this->Text   ( 145   , 68+$this->inTamY   , $rsItbi->getCampo('base_calculo'));
        else
            $this->Text   ( 145   , 68   , strtoupper($this->flValorTributoReal) );

        $this->Text   ( 183   , 68+$this->inTamY   , strtoupper($this->flImpostoAnualReal ) );
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 145   , 76.5+$this->inTamY , $rsItbi->getCampo('taxa') );
            $this->Text   ( 145   , 85.5+$this->inTamY , $rsItbi->getCampo('multa') );
            $this->Text   ( 183   , 76.5+$this->inTamY , $this->flTxAverbacao       );
        } else {
            $this->Text   ( 145   , 76.5 , strtoupper($this->flTaxaLimpezaAnual) );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $vlrTaxa = str_replace(',','.',str_replace('.','',$rsItbi->getCampo('taxa')));
            $vlrImp  = str_replace(',','.',str_replace('.','',$this->flValorAnualReal));
            $vlrAnual = $vlrImp+$vlrTaxa;

            //$this->Text   ( 183   , 85.5+$this->inTamY , number_format($vlrAnual,2,',','.') );
            $this->Text   ( 183   , 85.5+$this->inTamY , $this->flImpostoAnualReal); //number_format($this->flImpostoAnualReal,2,',','.') );
        } else {
            $this->Text   ( 183   , 76.5 , strtoupper($this->flValorAnualReal) );
            $this->Text   ( 183   , 85.5 , number_format($this->flTotalLancado,2,',','.') );
        }
        $this->Text   ( 115.5 , 85.5+$this->inTamY , strtoupper($this->inReferencia) );
        //limpar memoria
        unset($rsItbi,$obImovelVVenal);
    }
    /* Fim do posicionamento das variáveis */

    /* gera o PDF */
    public function show()
    {
        $this->output('Carne.pdf','D');
    }

    /* adiciona nova pagina */
    public function novaPagina()
    {
        $this->addPage();
    }
    /*
    *  Desenha informações complementares
    */
    public function drawComplemento($x,$y)
    {
        ;
        $this->setFont('Arial','',10);

        /* retangulos */
        $this->Rect( $x, $y, 92, 74 );
        $this->Rect( ($x+95), $y, 102, 74 );

        $this->setFont('Arial','BU',8);
        /* Cada nova linha sao mais 3.5 */
        /* esquerda */
        $this->Text   ( ($x+5)  , ($y+3.5)  , strtoupper('REDE AUTORIZADA PARA ARRECADAÇÃO VENCIMENTO DO') );
        $this->Text   ( ($x+40)  , ($y+7)   , strtoupper('IPTU-2006') );

        $this->setFont('Arial','',8);
        $this->Text   ( ($x+7)  , ($y+12)   , strtoupper('BRADESCO - BANCO DO BRASIL - BANESPA - BCN -') );
        $this->Text   ( ($x+6)  , ($y+15.5) , strtoupper('BANERJ - CAIXA ECONOMICA - HSBC - ITAU -REAL -') );
        $this->Text   ( ($x+5)  , ($y+19)   , strtoupper('UNIBANCO - CASAS LOTÉRICAS - SUPERMERCADOS') );
        $this->Text   ( ($x+25)  , ($y+22.5), strtoupper('CREDENCIADOS - BANCO POSTAL') );

        $this->setFont('Arial','BU',8);
        $this->Text   ( ($x+30)  , ($y+26)  , strtoupper('FORMA DE PAGAMENTO') );

        $this->setFont('Arial','',8);
        $this->Text   ( ($x+9)  , ($y+31) , strtoupper('AGÊNCIA BANCÁRIA INTERNET TELEFONE REDE AUTO') );
        $this->Text   ( ($x+32)  , ($y+34.5)  , strtoupper('ATENDIMENTO(24h)') );
        $this->setFont('Arial','',6);
        $this->Text   ( ($x+2)  , ($y+38)   , 'Dependendo dos serviços disponibilizados pelo Banco escolhido para efetuar o pagamento' ) ;
//      $this->Text   ( ($x+35)  , ($y+41.5), 'efetuar o pagamento') ;

        // direita
        $this->setFont('Arial','BU',8);
        $x +=92;
        $this->Text   ( ($x+12)  , ($y+3.5) , strtoupper('FATORES DE ATUALIZAÇÃO APÓS O VENCIMENTO') );
        $this->setFont('Arial','',8);
        $this->Text   ( ($x+5)  , ($y+12)   , 'MULTA: 5% no primeiro mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+15.5) , '              10% no segundo mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+19)   , '              15% no terceiro mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+22.5) , '              20% a partir do quarto mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+26)   , 'JUROS: ' );
        $this->Text   ( ($x+5)  , ($y+29.5) , '       1% ao mês ou fração, a partir do mês' );
        $this->Text   ( ($x+5)  , ($y+33)   , '       subsequente ao Vencimento ' );

        $this->setFont('Arial','BU',8);
        $this->Text   ( ($x+26)  , ($y+38)  , strtoupper('INFORMAÇÕES COMPLEMENTARES') );
        $this->setFont('Arial','',8);
        $this->Text   ( ($x+5)  , ($y+45)  , '- A segunda via do carnê poderá ser obtida via internet,ou na') ;
        $this->Text   ( ($x+5)  , ($y+48.5), '  Secretaria de fazenda, Rua 16 de Março, 183 - Centro.') ;
        $this->Text   ( ($x+5)  , ($y+52)  , '- Quando o pagamento ultrapassar o dia de vencimento previsto ') ;
        $this->Text   ( ($x+5)  , ($y+55.5), 'no carnê, o IPTU será devido com os acréscimos legais') ;
        $this->Text   ( ($x+5)  , ($y+59)  , '- Ao receber o carnê do IPTU o contribuinte deverá verificar ') ;
        $this->Text   ( ($x+5)  , ($y+63)  , '  se os dados estão corretos, e em caso de alguma divergência,') ;
        $this->Text   ( ($x+5)  , ($y+66.5), '  apresentar sua reclamação antes do vencimento da primeira') ;
        $this->Text   ( ($x+5)  , ($y+70)  , '  Cota Única, garantindo seus direitos') ;

    }
}

class RCarneDiversosPetropolis extends RProtocolo
{
    /* labels */
    public $lblTitulo1 = 'MATA DE SÃO JOÃO - Sec. de Adm. e Fin.';
    public $lblTitulo2 = 'IPTU';
    public $lblExercicio = 'EXERCÍCIO';
    public $lblInscricao = 'INSCRIÇÃO';
    public $lblCodDivida = 'CÓD. DÍVIDA';
    public $lblTributo   = 'TRIBUTO';
    public $lblParcela   = 'PARCELA';
    public $lblReferencia = 'REFERÊNCIA';
    public $lblDataProcessamento = 'DATA PROCESSAMENTO';
    public $lblVencimento = 'VENCIMENTO';
    public $lblValorCotaUnica = 'VALOR COTA ÚNICA';
    public $lblContribuinte = 'CONTRIBUINTE';
    public $lblData = 'DATA';
    public $lblCorrecao = 'CORREÇÃO';
    public $lblMonetaria = 'MONET(%)';
    public $lblObs       = 'OBSERVAÇÃO';

    public $lblMulta = '(+) MULTA DE MORA';
    public $lblJuros = '(+) JUROS DE MORA';
    public $lblOutros = '(+) ATUALIZAÇÃO MONETÁRIA';

    public $lblValorParcela = 'VALOR PARCELA';
    public $lblReal = '(REAL)';
    public $lblNumeracao = 'NOSSO NÚMERO';

    public $lblValorPrincipal = "(=) VALOR PRINCIPAL";
    public $lblValorTotal     = "(=) TOTAL";

    /* variaveis */
    public $ImagemCarne;
    public $stExercicio;
    public $inInscricao;
    public $inCodDivida;
    public $stTributo;
    public $stTributoAbrev;
    public $stTributoAbrev2;
    public $stTributoAbrev3;
    public $stParcela;
    public $inReferencia;
    public $dtProcessamento;
    public $dtVencimento;
    public $dtVencimentof1;
    public $dtVencimentof2;
    public $dtVencimentof3;
    public $flValorCotaUnica;
    public $flValor;
    public $flValorf1;
    public $flValorf2;
    public $flValorf3;
    public $stNomCgm;
    public $stBarCode;
    public $boParcelaUnica;
    public $stObservacaoL1;
    public $stObservacaoL2;
    public $stObservacaoL3;
    public $stObsVencimento; // = "Não receber após o vencimento.";
    public $stNumeracao;
    public $flValorMulta  = '0,00';
    public $flValorJuros  = '0,00';
    public $flValorOutros = '0,00';
    public $flValorTotal  = '0,00';
    public $tamY = 0.93;

    /* setters */
    public function setImagemCarne($valor) { $this->ImagemCarne      = $valor; }
    public function setExercicio($valor) { $this->stExercicio      = $valor; }
    public function setInscricao($valor) { $this->inInscricao      = $valor; }
    public function setCodDivida($valor) { $this->inCodDivida      = $valor; }
    public function setTributo($valor) { $this->stTributo        = $valor; }
    public function setTributoAbrev($valor) { $this->stTributoAbrev   = $valor; }
    public function setTributoAbrev2($valor) { $this->stTributoAbrev2  = $valor; }
    public function setTributoAbrev3($valor) { $this->stTributoAbrev3  = $valor; }
    public function setParcela($valor) { $this->stParcela        = $valor; }
    public function setReferencia($valor) { $this->inReferencia     = $valor; }
    public function setProcessamento($valor) { $this->dtProcessamento  = $valor; }
    public function setVencimento($valor) { $this->dtVencimento     = $valor; }
    public function setVencimento1($valor) { $this->dtVencimentof1   = $valor; }
    public function setVencimento2($valor) { $this->dtVencimentof2   = $valor; }
    public function setVencimento3($valor) { $this->dtVencimentof3   = $valor; }
    public function setValorCotaUnica($valor) { $this->flValorCotaUnica = $valor; }
    public function setValor($valor) { $this->flValor          = $valor; }
    public function setValor1($valor) { $this->flValorf1        = $valor; }
    public function setValor2($valor) { $this->flValorf2        = $valor; }
    public function setValor3($valor) { $this->flValorf3        = $valor; }
    public function setNomCgm($valor) { $this->stNomCgm         = $valor; }
    public function setBarCode($valor) { $this->stBarCode        = $valor; }
    public function setLinhaCode($valor) { $this->stLinhaCode      = $valor; }
    public function setParcelaUnica($valor) { $this->boParcelaUnica   = $valor; }
    public function setObservacaoL1($valor) { $this->stObservacaoL1   = $valor; }
    public function setObservacaoL2($valor) { $this->stObservacaoL2   = $valor; }
    public function setObservacaoL3($valor) { $this->stObservacaoL3   = $valor; }
    public function setObsVencimento($valor) { $this->stObsVencimento  = $valor; }
    public function setNumeracao($valor) { $this->stNumeracao      = $valor; }
    public function setValorTotal($valor) { $this->flValorTotal     = $valor; }

    /* getters */
    public function getImagemCarne() { return $this->ImagemCarne      ; }
    public function getExercicio() { return $this->stExercicio      ; }
    public function getInscricao() { return $this->inInscricao      ; }
    public function getCodDivida() { return $this->inCodDivida      ; }
    public function getTributo() { return $this->stTributo        ; }
    public function getTributoAbrev() { return $this->stTributoAbrev   ; }
    public function getParcela() { return $this->stParcela        ; }
    public function getReferencia() { return $this->inReferencia     ; }
    public function getProcessamento() { return $this->dtProcessamento  ; }
    public function getVencimento() { return $this->dtVencimento     ; }
    public function getVencimento1() { return $this->dtVencimentof1   ; }
    public function getVencimento2() { return $this->dtVencimentof2   ; }
    public function getVencimento3() { return $this->dtVencimentof3   ; }
    public function getValorCotaUnica() { return $this->flValorCotaUnica ; }
    public function getValor() { return $this->flValor          ; }
    public function getValor1() { return $this->flValorf1        ; }
    public function getValor2() { return $this->flValorf2        ; }
    public function getValor3() { return $this->flValorf3        ; }
    public function getNomCgm() { return $this->stNomCgm         ; }
    public function getBarCode() { return $this->stBarCode        ; }
    public function getLinhaCode() { return $this->stLinhaCode      ; }
    public function getParcelaUnica() { return $this->boParcelaUnica   ; }
    public function getObservacaoL1() { return $this->stObservacaoL1   ; }
    public function getObservacaoL2() { return $this->stObservacaoL2   ; }
    public function getObservacaoL3() { return $this->stObservacaoL3   ; }
    public function getObsVencimento() { return $this->stObsVencimento  ; }
    public function getNumeracao() { return $this->stNumeracao      ; }

    /* configura carne */
    public function configuraCarne()
    {
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
    }

    /* layout do carne */
    public function drawCarne($x, $y)
    {
        // truncar tributo
        if ( !$this->stTributoAbrev )
            $this->stTributoAbrev = substr($this->stTributo,0,25);

        $this->stNomCgm  = substr($this->stNomCgm ,0,80);
        $this->setFont( 'Arial','',10 );

        /* posiciona imagem */
        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, $x, $y, 17, 10, $stExt );
        }

        /* returna retangulo */
        $this->Rect( $x, $y, 72, 104*$this->tamY );
        $this->Rect( ($x+75), $y, 122, 84*$this->tamY );

        /* linha horizontais */
        $this->Line( $x, ($y+(11*$this->tamY)), (92-20+$x), ($y+(11*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(11*$this->tamY)), ($x+197), ($y+(11*$this->tamY)) );

        $this->Line( $x, ($y+(18*$this->tamY)), (92-20+$x), ($y+(18*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(18*$this->tamY)), ($x+197), ($y+(18*$this->tamY)) );

        $this->Line( $x, ($y+(25*$this->tamY)), (92-20+$x), ($y+(25*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(25*$this->tamY)), ($x+197), ($y+(25*$this->tamY)) );

        $this->Line( $x+46, ($y+(32*$this->tamY)), (92-20+$x), ($y+(32*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(32*$this->tamY)), ($x+197), ($y+(32*$this->tamY)) );

        $this->Line( $x+36, ($y+(39*$this->tamY)), (92-20+$x), ($y+(39*$this->tamY)) );
        $this->Line( ($x+151-20), ($y+(39*$this->tamY)), ($x+197), ($y+(39*$this->tamY)) );

        $this->Line( $x, ($y+(46*$this->tamY)), (92-20+$x), ($y+(46*$this->tamY)) );
        // linhas adicionais no carne no lado esquerdo

        $this->Line( $x, ($y+(68*$this->tamY)), (92-20+$x), ($y+(68*$this->tamY)) ); //nova

        $this->Line( $x, ($y+(53*$this->tamY)), (92-20+$x), ($y+(53*$this->tamY)) );
        $this->Line( $x, ($y+(60*$this->tamY)), (92-20+$x), ($y+(60*$this->tamY)) );

        $this->Line( ($x+95-20), ($y+(46*$this->tamY)), ($x+197), ($y+(46*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(54*$this->tamY)), ($x+197), ($y+(54*$this->tamY)) );

        /* linhas verticais */
        $this->Line( ($x+56-10), ($y+(11*$this->tamY)), ($x+56-10), ($y+(46*$this->tamY)) );
        $this->Line( ($x+18), ($y+(18*$this->tamY)), ($x+18), ($y+(25*$this->tamY)) );

        $this->Line( ($x+151), ($y+(11*$this->tamY)), ($x+151), ($y+(35*$this->tamY)) );
        $this->Line( ($x+113), ($y+(18*$this->tamY)), ($x+113), ($y+(25*$this->tamY)) );

        /* brazao */
        if ($this->Imagem) {
            $stExt = substr( $this->Imagem, strlen($this->Imagem)-3, strlen($this->Imagem) );
            $this->Image( $this->Imagem, 8, 9, 25, 16.5, $stExt );
        }

        $this->setFont('Arial','B',8);
        $this->Text   ( ($x+27) , ($y+(4*$this->tamY)) , "MATA DE SÃO JOÃO" );
        $this->Text   ( ($x+27) , ($y+(7*$this->tamY)) , "Sec. de Adm. e Fin." );

        $this->Text   ( ($x+122-20), ($y+(4*$this->tamY)) , $this->lblTitulo1 );

        $this->Text   ( ($x+27) , ($y+(10*$this->tamY)), $this->lblExercicio );
        $this->Text   ( ($x+117-20), ($y+(10*$this->tamY)), $this->lblExercicio );

        $this->setFont('Arial'  ,'B',6);
        $this->Text   ( ($x+1)  , ($y+(13.5*$this->tamY)), $this->lblNumeracao    );
        $this->Text   ( ($x+96-20) , ($y+(13.5*$this->tamY)), $this->lblNumeracao    );

        $this->Text   ( ($x+1)  , ($y+(20.5*$this->tamY)), $this->lblParcela      );
        $this->Text   ( ($x+96-20) , ($y+(20.5*$this->tamY)), $this->lblParcela      );
        $this->Text   ( ($x+27) , ($y+(20.5*$this->tamY)), $this->lblInscricao      );
        $this->Text   ( ($x+122), ($y+(20.5*$this->tamY)), $this->lblInscricao    );

        $this->Text   ( ($x+1)  , ($y+(27.5*$this->tamY)),  $this->lblTributo   );
        $this->Text   ( ($x+96-20) , ($y+(27.5*$this->tamY)), $this->lblTributo   );

        /* retangulo para vencimento */
        $this->setFillColor( 240 );
        $this->Rect   ( ($x), ($y+(39*$this->tamY)), 46, 7*$this->tamY, 'DF' );
        $this->Rect   ( ($x+95-20), ($y+(32*$this->tamY)), 56+20, 14*$this->tamY, 'DF' );
        $this->setFillColor( 0 );

        $this->Text   ( ($x+1)  , ($y+(41.5*$this->tamY)), $this->lblVencimento   );
        $this->Text   ( ($x+96-20) , ($y+(34.5*$this->tamY)), $this->lblVencimento   );

        $this->Text   ( ($x+58-10) , ($y+(13.5*$this->tamY)), $this->lblValorPrincipal   );
        $this->Text   ( ($x+154), ($y+(13.5*$this->tamY)), $this->lblValorPrincipal   );
        $this->Text   ( ($x+58-10) , ($y+(20.5*$this->tamY)), $this->lblMulta    );
        $this->Text   ( ($x+154), ($y+(20.5*$this->tamY)), $this->lblMulta    );
        $this->Text   ( ($x+58-10) , ($y+(27.5*$this->tamY)), $this->lblJuros    );
        $this->Text   ( ($x+154), ($y+(27.5*$this->tamY)), $this->lblJuros    );
        $this->Text   ( ($x+58-10) , ($y+(34.5*$this->tamY)), "(+) AT. MONET."   );
        $this->Text   ( ($x+154), ($y+(34.5*$this->tamY)), $this->lblOutros   );
        $this->Text   ( ($x+58-10) , ($y+(41.5*$this->tamY)), $this->lblValorTotal );
        $this->Text   ( ($x+154), ($y+(41.5*$this->tamY)), $this->lblValorTotal );

        // nao receber apos vencimento
        if ( substr( $this->stLinhaCode, 0, 3) == '817' ) { //diferente de unica aparece esta mensagem
        //if ( !$this->getObsVencimento() ) {
            $this->setObsVencimento ( 'Receber até 31/12/'.Sessao::read( "exercicio" ).'.' );
        }
        //}

        $this->Text   ( ($x+1)  , ($y+(45*$this->tamY)), $this->getObsVencimento() );
        $this->Text   ( ($x+96-20) , ($y+(45*$this->tamY)), $this->getObsVencimento() );
//67.5, 60.5, 53.5
        // contribuinte
        $this->Text   ( ($x+1)  , ($y+(55.5*$this->tamY)), $this->lblContribuinte );
        $this->Text   ( ($x+96-20) , ($y+(48.5*$this->tamY)), $this->lblContribuinte );

        // endereço do imovel
        $this->Text   ( ($x+1)  , ($y+(62.5*$this->tamY)), 'ENDEREÇO DO IMÓVEL' );

        // obs
        $this->Text   ( ($x+1)  , ($y+(71*$this->tamY) ), $this->lblObs          );
        $this->Text   ( ($x+76)  , ($y+(57*$this->tamY) ), $this->lblObs          );

        // vias
        $this->setFont( 'Arial','I',6 );
        $this->Text   ( ($x+70-20) , ($y+(71*$this->tamY)), 'VIA CONTRIBUINTE');
        $this->Text   ( ($x+178), ($y+(56.5*$this->tamY)), 'VIA PREFEITURA');

        $this->setFont( 'Arial','', 6 );
        $this->Text   ( 8, $y+(49*$this->tamY), "MULTA 5% Até 30 DIAS, 10% DE 30 A 60 DIAS E 15% SUPERIOR A 60" );
        $this->Text   ( 8, $y+(51*$this->tamY), " DIAS. JUROS DE 1% AO MÊS = 0,033%" );

        $stObs = str_replace("\n\r"," ",$this->stObservacao);
        $this->Text   ( 8     , $y+(74*$this->tamY)  , substr($stObs,0      ,62 ));
        $this->Text   ( 8     , $y+(77*$this->tamY)  , substr($stObs,62     ,60 ));
        $this->Text   ( 8     , $y+(80*$this->tamY)  , substr($stObs,122    ,60 ));
        $this->Text   ( 8     , $y+(83*$this->tamY)  , substr($stObs,182    ,60 ));
        $this->Text   ( 8     , $y+(86*$this->tamY)  , substr($stObs,242    ,60 ));
        $this->Text   ( 8     , $y+(89*$this->tamY)  , substr($stObs,302    ,60 ));

        $this->Text   ( $x+80 , $y+(60*$this->tamY)  , substr($stObs, 0, 110 ));
        $this->Text   ( $x+80 , $y+(63*$this->tamY)  , substr($stObs, 110, 110 ));
        $this->Text   ( $x+80 , $y+(66*$this->tamY)  , substr($stObs, 220, 110 ));
        $this->Text   ( $x+80 , $y+(69*$this->tamY)  , substr($stObs, 330, 110 ));
        $this->Text   ( $x+80 , $y+(72*$this->tamY)  , substr($stObs, 440, 110 ));
        $this->Text   ( $x+80 , $y+(75*$this->tamY)  , substr($stObs, 550, 110 ));
    }

    /* posiciona variaveis no carne */
    public function posicionaVariaveis($x, $y)
    {
        $this->setFont('Arial', 'B', 8 );
        //falta aki
        $this->Text   ( ($x+43) , ($y+(10*$this->tamY)), Sessao::read( "exercicio" ) ); // ok
        $this->Text   ( ($x+133-20), ($y+(10*$this->tamY)), Sessao::read( "exercicio" ) ); // ok

        $this->Text   ( ($x+72-20) , ($y+(17*$this->tamY)), $this->flValor ); // ok
        $this->Text   ( ($x+166), ($y+(17*$this->tamY)), $this->flValor ); // ok
        $this->Text   ( ($x+72-20) , ($y+(23*$this->tamY)), $this->flValorMulta ); // ok  multa
        $this->Text   ( ($x+166), ($y+(23*$this->tamY)), $this->flValorMulta ); // ok  multa
        $this->Text   ( ($x+72-20) , ($y+(31*$this->tamY)), $this->flValorJuros ); // ok  juros
        $this->Text   ( ($x+166), ($y+(31*$this->tamY)), $this->flValorJuros ); // ok  juros
        $this->Text   ( ($x+72-20) , ($y+(38*$this->tamY)), $this->flValorOutros); // ok  outros
        $this->Text   ( ($x+166), ($y+(38*$this->tamY)), $this->flValorOutros); // ok  outros
        $this->Text   ( ($x+72-20) , ($y+(45*$this->tamY)), $this->flValorTotal ); // ok  total
        $this->Text   ( ($x+166), ($y+(45*$this->tamY)), $this->flValorTotal ); // ok  total

//57
        $this->Text   ( ($x+2), ($y+(59*$this->tamY)), $this->getNomCgm() ); // ok  contribuinte
        $this->Text   ( ($x+106-20),($y+(52*$this->tamY)), $this->getNomCgm() ); // ok  contribuinte

        $this->Text   ( ($x+2), ($y+(66*$this->tamY)), substr($this->getRua(), 0, 46) ) ; // end . do imovel

        $this->Text   ( ($x+14) , ($y+(17*$this->tamY))  , $this->stNumeracao ); // ok
        $this->Text   ( ($x+110-20), ($y+(17*$this->tamY))  , $this->stNumeracao ); // ok

        $this->Text   ( ($x+2) , ($y+(31*$this->tamY))  , $this->stTributoAbrev ); // ok
        $this->Text   ( ($x+2) , ($y+(34*$this->tamY))  , $this->stTributoAbrev2 ); // ok
        $this->Text   ( ($x+2) , ($y+(37*$this->tamY))  , $this->stTributoAbrev3 ); // ok

        $this->Text   ( ($x+110-34), ($y+(31*$this->tamY))  , $this->stTributo ); // ok

        $this->Text   ( ($x+05) , ($y+(24*$this->tamY))  , $this->stParcela   ); // ok
        $this->Text   ( ($x+101-20), ($y+(24*$this->tamY))  , $this->stParcela   ); // ok

        $this->Text   ( ($x+20) , ($y+(24*$this->tamY))  , $this->inInscricao   ); // ok
        $this->Text   ( ($x+115), ($y+(24*$this->tamY))  , $this->inInscricao   ); // ok

        $this->setFont('Arial', 'B', 12 );
        $this->Text   ( ($x+20) , ($y+(43*$this->tamY)), $this->dtVencimento ); // ok
        $this->Text   ( ($x+110-20), ($y+(40*$this->tamY)), $this->dtVencimento ); // ok
        $this->setFont('Arial', '', 8 );

        $this->Text   ( ($x+95-20), ($y+(89*$this->tamY)), $this->stLinhaCode );
        $this->defineCodigoBarras( ($x+95-15), ($y+(91*$this->tamY)), $this->stBarCode );
    }

    /* adiciona nova pagina */
    public function novaPagina()
    {
        $this->addPage();
    }

    /* habilita e desabilita a quebra de pagina automatica */
    public function setQuebraPagina($valor)
    {
        $this->setAutoPageBreak( $valor, 1 );
    }

    /* picote entre os carnes */
    public function setPicote($x, $y, $firstPage = false)
    {
        for ($i=0;$i<=196;($i+=2)) {
           $this->Line( ($x+$i), ($y+(106*$this->tamY)), ($x+$i+1), ($y+(106*$this->tamY)) );
        }

        for (($i=-3);$i<=105;($i+=2)) {
            $this->Line( ($x+93.5-20), ($y+($i*$this->tamY)), ($x+93.5-20), ($y+(($i+1)*$this->tamY)) );
        }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneDadosCadastraisMataSaoJoao extends RCarneDiversosPetropolis
{
    /**
     * @access public
     * @var String Texto de Composição do Calculo
     */
    public $stComposicaoCalculo ;
    /**
     * @access public
     * @var String
     */
    public $stNomePrefeitura ;
    /**
     * @access public
     * @var String
     */
    public $stSubTitulo ;
    /**
     * @access public
     * @var String
     */
    public $stExercicio ;
    /**
     * @access public
     * @var String
     */
    public $stContribuinte ;
    /**
     * @access public
     * @var String
     */
    public $stInscricaoCadastral ;
    /**
     * @access public
     * @var String
     */
    public $stCategoriaUtilizacao ;
    /**
     * @access public
     * @var String
     */
    public $stTipoTributo ;
    /**
     * @access public
     * @var String
     */
    public $stCodigoLogradouro ;
    /**
     * @access public
     * @var String
     */
    public $stNomeLogradouro ;
    /**
     * @access public
     * @var String
     */
    public $stComplemento ;
    /**
     * @access public
     * @var String
     */
    public $stQuadra ;
    /**
     * @access public
     * @var String
     */
    public $stLote ;
    /**
     * @access public
     * @var String
     */
    public $stDistrito ;
    /**
     * @access public
     * @var String
     */
    public $stRegiao ;
    /**
     * @access public
     * @var String
     */
    public $stCep ;
    /**
     * @access public
     * @var String
     */
    public $stCidade ;
    /**
     * @access public
     * @var String
     */
    public $stEstado ;
    /**
     * @access public
     * @var String
     */
    public $stAreaUsoPrivativoTerreno ;
    /**
     * @access public
     * @var String
     */
    public $stVupt ;
    /**
     * @access public
     * @var String
     */
    public $stVupc;
    /**
     * @access public
     * @var String
     */
    public $stValorVenalTerreno;
    /**
     * @access public
     * @var String
     */
    public $stImpostoTerritorial;
    /**
     * @access public
     * @var String
     */
    public $stAreaUsoPrivativoEdificacao;
    /**
     * @access public
     * @var String
     */
    public $stValorVenalConstrucao;
    /**
     * @access public
     * @var String
     */
    public $stImpostoPredial;
    /**
     * @access public
     * @var String
     */
    public $stAliquota;
    /**
     * @access public
     * @var String
     */
    public $stValorVenalImovel;
    /**
     * @access public
     * @var String
     */
    public $stValorImposto;

    /**
     * @access public
     * @var String
     */
    public $stCondominio;

    /**
     * @access public
     * @var String
     */
    public $stTipoUnidade;
    /**
     * @access public
     * @var String
     */
    public $stZona;
    /**
     * @access public
     * @var String
     */
    public $stAreaM2;
    /**
     * @access public
     * @var String
     */
    public $stValorM2;
    /**
     * @access public
     * @var String
     */
    public $stValorTaxa;
    /**
     * @access public
     * @var String
     */
    public $stValorTotalTributos;
    /**
     * @access public
     * @var String
     */
    public $stContribIlumPublica;
    /**
     * @access public
     * @var String
     */
    public $arDemonstrativoParcelas;
    /**
     * @access public
     * @var String
     */
    public $arVencimentosDemonstrativos;

    public $stCamLogo;

    public function RCarneDadosCadastraisMataSaoJoao()
    {
        ;

        parent::RCarneDiversosPetropolis();
        /**
         * Seta Informações Basicas da Prefeitura
         */
        $this->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->stSubTitulo = 'Secretaria de Administração e Finanças';
        $this->lblMulta = '(+) MULTA DE MORA';
        $this->lblJuros = '(+) JUROS DE MORA';
        $this->lblOutros = '(+) ATUALIZAÇÃO MONETÁRIA';

        /**
         * Seta Configuração do PDF
         */
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
        $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );

        $stNomeImagem = $rsListaImagens->getCampo("valor");
        $this->stCamLogo = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'.$stNomeImagem;
    }

    public function desenhaCarne($x , $y)
    {
$stComposição =
"                                                                          COMPOSIÇÂO DO CÁLCULO\n
I- Imposto Sobre a Propriedade Predial e Territorial Urbana (IPTU) => Valor do Imposto = Valor Venal x Alíquota\n
   Valor Venal do Imóvel = Valor Venal do Terreno (VUPt x área m2) + Valor Venal da Edificação (VUPC x área m2).\n
\n
II- Taxa de Limpeza Pública =>  Valor da Taxa = Área (do terreno, caso não haja edificação; havendo usar área da construção) x valor/m2\n
\n
III- Contribuição de Iluminação Pública => Área do Terreno x R$0,05 (cobrada no carnê de IPTU apenas para os imóveis sem edificação)\n
\n
Observações:\n
1) VUPt é o Valor do metro quadrado do terreno e VUPc o valor do metro quadrado da construção;\n
(estes valores são definidos a Planta Genérica de Valores do Município, Lei n.248/2005, alterada pela Lei nº 281/2006).\n
\n
2) O recolhimento fora do prazo enseja a cobrança dos seguintes acréscimos, calculados sobre o valor original atualizado monetariamente:\n
  - Multa de mora de 5 % (cinco por cento), se o tributo for pago no prazo de 30 (trinta)dias, após o vencimento; 10% (dez por cento), se o atraso for\n
superior a 30(trinta) dias, e até 60 (sessenta) dias; 15% (quinze por cento), se o atraso for superior a 60 (sessenta) dias. (Fundamentação Legal:\n
Art 60, da Lei nº 280/2006);\n
  - Juros de mora de 1% (um por cento) ao mes calendário ou fração, a razão de 0,033% ao dia limitando ao máximo de 1%, calculado á data do seu\n
  pagamento. (fundamentação Legal: Artigo.60 da Lei n.280/2006).\n
  - Multa de Infração quando for o caso. (fundamentação Legal: Artigo.60 da Lei n.280/2006);\n
\n
3) Os tributos poderão ser pagos em até 11 parcelas, com valor mínimo de R$ 10,00 cada (Fundamentação Legal: Calendário Fiscal em Vigência);\n
\n
4) Necessitando alterar os dados cadastrais, compareça, munido de documentos comprobatórios, ao Setor de Tributos e Fiscalização, órgão da \n
Secretaria Municipal de Administração e Finanças, sito no Centro Administrativo, Rua Luiz Antônio Garcez, s/nº - Centro - CEP: 48.280-000 - Mata \n
de São João/Ba. Poderá comparecer também ao posto de Apoio Tributário, sito no Shopping Armazém da Vila, 1º andar, sala 41 - \n
Praia do Forte - Mata de São João/Ba. Telefones: (71) 3635-1669/3294 (Sede) / (71) 3676-1569 (Praia do Forte) / (71) 9617-7231.\n
";

        $inTamY = 1.14;

        //pagina 1
        /**
         * Retangulos
         */
        /* Retangulo da Capa */
        //$this->Rect( $x, $y, 189, 88*$inTamY ); //o valor do y era 93
        /* Retangulo Mata Feliz */
        //$this->Rect( $x, $y + (100*$inTamY) , 189, 88*$inTamY );
/*

        $this->Rect( $x+10, $y+(14*$inTamY), 130, 42*$inTamY ); //retangulo do endereco de entrega
        $this->Rect( $x+10, $y+(60*$inTamY), 170, 25*$inTamY ); //retangulo de uso dos correios

        $this->Rect( $x+134, $y+(62*$inTamY), 42, 22*$inTamY ); //retangulo dentro do retangulo de uso dos correios
        $this->Line( $x+134, $y+(68*$inTamY), $x+176, $y+(68*$inTamY) );
        $this->Line( $x+134, $y+(76*$inTamY), $x+176, $y+(76*$inTamY) );

        $stCorreio = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/correiomata.jpg";
        $stExt = substr( $stCorreio, strlen($stCorreio)-3, strlen($stCorreio) );
        $this->Image( $stCorreio, $x+142 , $y+(18*$inTamY) , 34 , 34 , $stExt);

        $this->setFont( 'Arial','B',10 );
        $this->Text( $x+32, $y+(4*$inTamY), 'P R E F E I T U R A    M U N I C I P A L    D E    M A T A    D E    S A O    J O A O' );
        $this->setFont( 'Arial','B',16 );
        $this->Text( $x+62, $y+(10*$inTamY), 'IPTU/TLP/CIP 2008' );

        $this->setFont( 'Arial','B',10 );
        $this->Text( $x+32, $y+(22*$inTamY), 'ENDEREÇO DE ENTREGA DESTE CARNE' );
        $this->Text( $x+14, $y+(32*$inTamY), 'INSCRICAO:' );
        $this->Text( $x+14, $y+(36*$inTamY), 'CONTRIBUINTE:' );
        $this->Text( $x+14, $y+(42*$inTamY), 'ENDEREÇO:' );

        $this->setFont( 'Arial','',10 );
        $this->Text( $x+48, $y+(32*$inTamY), $this->stInscricaoCadastral );
        $this->Text( $x+48, $y+(36*$inTamY), $this->stContribuinte );

        $this->Text( $x+14, $y+(46*$inTamY), $this->stNomeLogradouro.' '.$this->stComplemento );
        $this->Text( $x+14, $y+(50*$inTamY), 'CEP: '.$this->stCep);
        $this->Text( $x+14, $y+(54*$inTamY), $this->stCidade.' / '.$this->stEstado );

        $this->setFont( 'Arial','',16 );
        $this->Text( $x+12, $y+(66*$inTamY), 'PARA USO DOS CORREIOS' );

        $this->setFont( 'Arial','B',9 );
        $this->Text( $x+20, $y+(70*$inTamY), '[ ] ENDEREÇO INSUFICIENTE              [ ] RECUSADO' );
        $this->Text( $x+20, $y+(74*$inTamY), '[ ] NÃO EXISTE Nº INDICADO               [ ] NÃO PROCURADO' );
        $this->Text( $x+20, $y+(78*$inTamY), '[ ] DESCONHECIDO                               [ ] INF. ESCRITA POR TERCEIROS' );
        $this->Text( $x+20, $y+(82*$inTamY), '[ ] OUTRO: ___________________________________________________' );

        $this->setFont( 'Arial','B',8 );
        $this->Text( $x+136, $y+(65*$inTamY), 'DATA:' );
        $this->Text( $x+136, $y+(71*$inTamY), 'ENDEREÇO:' );
        $this->Text( $x+136, $y+(79*$inTamY), 'REINTEGRAÇÂO POSTAL' );
        $this->Text( $x+136, $y+(83*$inTamY), 'EM:                /           /' );

        //segundo retangulo
        $this->setFont( 'Arial', '', 12 );
        $this->Text( $x+3, $y+(115*$inTamY), 'EM MATA DE SÃO JOÃO, AGORA É ASSIM: A GENTE TRABALHA PARA VOCÊ SER FELIZ!' );
        $this->Text( $x+3, $y+(120*$inTamY), 'TUDO ESTÁ SENDO FEITO PARA DEIXAR O MUNICÍPIO MAIS MODERNO, MAIS LIMPO,' );
        $this->Text( $x+3, $y+(125*$inTamY), 'MAIS JUSTO, MELHOR DE SE VIVER. MAS ISSO SÓ É POSSÍVEL, SE VOCÊ FIZER A' );
        $this->Text( $x+3, $y+(130*$inTamY), 'SUA PARTE, PAGANDO SEU IPTU EM DIA. CONTRIBUA COM O NOSSO TRABALHO. UM' );
        $this->Text( $x+3, $y+(135*$inTamY), 'MUNICÍPIO MAIS BONITO A GENTE PAGA PARA VER.' );

        $this->setFont( 'Arial', 'B', 12 );
        $this->Text( $x+44, $y+(165*$inTamY), 'JOÃO GUALBERTO VASCONCELOS' );

        $this->setFont( 'Arial', '', 12 );
        $this->Text( $x+50, $y+(170*$inTamY), 'P R E F E I T O    M U N I C I P A L' );

        $this->addPage();
        //pagina 2

        // Retangulo telefones uteis
        $this->Rect( $x, $y, 189, 88*$inTamY );
        // Retangulo instrucoes de pagamento
        $this->Rect( $x, $y + (100*$inTamY) , 189, 88*$inTamY );

        $this->setFont( 'Arial', 'B', 18 );
        $this->SetFillColor(240, 240, 240);
        $this->Rect( $x, $y, 189, 12*$inTamY, 'DF');
        $this->Text( $x+45, $y+(8*$inTamY), 'T E L E F O N E S    Ú T E I S' );

        $this->setFont( 'Arial', '', 12 );
        $this->Text( $x+22, $y+(18*$inTamY), 'Prefeitura de Mata de São João: (71) 3635-13-10/1992 Fax: (71) 3635-1668' );
        $this->Text( $x+22, $y+(22*$inTamY), 'Site: www.pmsj.ba.gov.br' );

        $this->Rect( $x+2, $y+(25*$inTamY), 90, 62*$inTamY );

        $this->setFont( 'Arial', 'B', 8 );
        $this->SetFillColor(240, 240, 240);
        $this->Rect( $x+2, $y+(25*$inTamY), 90, 4*$inTamY, 'DF');
        $this->Text( $x+14, $y+(28*$inTamY), 'SECRETARIAS/SETORES DA PREFEITURA' );

        $this->setFont( 'Arial', '', 8 );

        $this->Text( $x+3, $y+(32*$inTamY), 'Secretaria de Administração e Finanças' );
        $this->Text( $x+64, $y+(32*$inTamY), '(71) 3635-1310' );
        $this->Line( $x+2, $y+(33*$inTamY), $x+92, $y+(33*$inTamY) );

        $this->Text( $x+3, $y+(36*$inTamY), 'Secretaria de Obras e Serviços Públicos' );
        $this->Text( $x+64, $y+(36*$inTamY), '(71) 3635-1058' );
        $this->Line( $x+2, $y+(37*$inTamY), $x+92, $y+(37*$inTamY) );

        $this->Text( $x+3, $y+(40*$inTamY), 'Secretaria de Planejamento, Desenvolvi-' );
        $this->Text( $x+3, $y+(42*$inTamY), 'mento e Meio Ambiente' );
        $this->Text( $x+64, $y+(40*$inTamY), '(71) 3676-0374' );
        $this->Line( $x+2, $y+(43*$inTamY), $x+92, $y+(43*$inTamY) );

        $this->Text( $x+3, $y+(46*$inTamY), 'Secretaria de Cultura e Turismo' );
        $this->Text( $x+64, $y+(46*$inTamY), '(71) 3635-2409' );
        $this->Line( $x+2, $y+(47*$inTamY), $x+92, $y+(47*$inTamY) );

        $this->Text( $x+3, $y+(50*$inTamY), 'Secretaria de Saúde' );
        $this->Text( $x+64, $y+(50*$inTamY), '(71) 3635-1628' );
        $this->Line( $x+2, $y+(51*$inTamY), $x+92, $y+(51*$inTamY) );

        $this->Text( $x+3, $y+(54*$inTamY), 'Secretaria de Educação' );
        $this->Text( $x+64, $y+(54*$inTamY), '(71) 3635-1084' );
        $this->Line( $x+2, $y+(55*$inTamY), $x+92, $y+(55*$inTamY) );

        $this->Text( $x+3, $y+(58*$inTamY), 'Secretaria de Trabalho e Ação Social' );
        $this->Text( $x+64, $y+(58*$inTamY), '(71) 3635-1666' );
        $this->Line( $x+2, $y+(59*$inTamY), $x+92, $y+(59*$inTamY) );

        $this->Text( $x+3, $y+(62*$inTamY), 'Secretaria de Agricultura' );
        $this->Text( $x+64, $y+(62*$inTamY), '(71) 3635-1559' );
        $this->Line( $x+2, $y+(63*$inTamY), $x+92, $y+(63*$inTamY) );

        $this->Text( $x+3, $y+(66*$inTamY), 'Procuradoria Geral do Município' );
        $this->Text( $x+64, $y+(66*$inTamY), '(71) 3635-1129' );
        $this->Line( $x+2, $y+(67*$inTamY), $x+92, $y+(67*$inTamY) );

        $this->Text( $x+3, $y+(70*$inTamY), 'Setor de Tributos e Fiscalização' );
        $this->Text( $x+64, $y+(70*$inTamY), '(71) 3635-1669' );
        $this->Text( $x+3, $y+(72*$inTamY), '(Coordenadoria Fazendária do Município)' );
        $this->Text( $x+64, $y+(72*$inTamY), '(71) 3635-3294' );

        $this->Line( $x+62, $y+(29*$inTamY), $x+62, $y+(73*$inTamY) );

        $this->setFont( 'Arial', 'B', 8 );
        $this->SetFillColor(240, 240, 240);
        $this->Rect( $x+2, $y+(73*$inTamY), 90, 4*$inTamY, 'DF');
        $this->Text( $x+14, $y+(76*$inTamY), 'Hospital Municipal Dr. Eurico Goulart de Freitas' );

        $this->setFont( 'Arial', '', 8 );
        $this->Text( $x+35, $y+(80*$inTamY), '(71) 3635-1005' );
        $this->Text( $x+35, $y+(83*$inTamY), '(71) 3635-1318' );
        $this->Text( $x+35, $y+(86*$inTamY), '(71) 3635-3850' );

        $this->Rect( $x+94, $y+(25*$inTamY), 90, 62*$inTamY ); //lado b

        $this->setFont( 'Arial', 'B', 8 );
        $this->SetFillColor(240, 240, 240);
        $this->Rect( $x+94, $y+(25*$inTamY), 90, 4*$inTamY, 'DF');
        $this->Text( $x+108, $y+(28*$inTamY), 'OUTROS TELEFONES ÚTEIS' );

        $this->setFont( 'Arial', '', 8 );

        $this->Text( $x+95, $y+(32*$inTamY), 'Câmara Municipal de Vereadores' );
        $this->Text( $x+156, $y+(32*$inTamY), '(71) 3635-3565' );
        $this->Line( $x+94, $y+(33*$inTamY), $x+184, $y+(33*$inTamY) );

        $this->Text( $x+95, $y+(36*$inTamY), 'Fórum Desembargador Francisco Ponde' );
        $this->Text( $x+95, $y+(38*$inTamY), 'Sobrinho' );
        $this->Text( $x+156, $y+(36*$inTamY), '(71) 3635-1303' );
        $this->Line( $x+94, $y+(39*$inTamY), $x+184, $y+(39*$inTamY) );

        $this->Text( $x+95, $y+(42*$inTamY), 'Juizado de Menores' );
        $this->Text( $x+156, $y+(42*$inTamY), '(71) 3635-1303' );
        $this->Line( $x+94, $y+(43*$inTamY), $x+184, $y+(43*$inTamY) );

        $this->Text( $x+95, $y+(46*$inTamY), 'Conselho Tutelar' );
        $this->Text( $x+156, $y+(46*$inTamY), '(71) 3635-2039' );
        $this->Line( $x+94, $y+(47*$inTamY), $x+184, $y+(47*$inTamY) );

        $this->Text( $x+95, $y+(50*$inTamY), 'Delegacia de Polícia Civil' );
        $this->Text( $x+156, $y+(50*$inTamY), '(71) 3635-1090' );
        $this->Line( $x+94, $y+(51*$inTamY), $x+184, $y+(51*$inTamY) );

        $this->Text( $x+95, $y+(54*$inTamY), 'ILUMITEC (Iluminação Pública)' );
        $this->Text( $x+156, $y+(54*$inTamY), '0800 28 08 8000' );
        $this->Text( $x+156, $y+(56*$inTamY), '(71) 3635-3295' );
        $this->Line( $x+94, $y+(57*$inTamY), $x+184, $y+(57*$inTamY) );

        $this->Text( $x+95, $y+(60*$inTamY), 'Banco do Brasil' );
        $this->Text( $x+156, $y+(60*$inTamY), '(71) 3635-1098' );
        $this->Line( $x+94, $y+(61*$inTamY), $x+184, $y+(61*$inTamY) );

        $this->Text( $x+95, $y+(64*$inTamY), 'Correios' );
        $this->Text( $x+156, $y+(64*$inTamY), '(71) 3635-1059' );
        $this->Line( $x+94, $y+(65*$inTamY), $x+184, $y+(65*$inTamY) );

        $this->Text( $x+95, $y+(68*$inTamY), 'Previdência Social' );
        $this->Text( $x+156, $y+(68*$inTamY), '(71) 3635-1247/1605' );
        $this->Line( $x+94, $y+(69*$inTamY), $x+184, $y+(69*$inTamY) );

        $this->Text( $x+95, $y+(72*$inTamY), 'Incra' );
        $this->Text( $x+156, $y+(72*$inTamY), '(71) 3635-3542' );
        $this->Line( $x+94, $y+(73*$inTamY), $x+184, $y+(73*$inTamY) );

        $this->Text( $x+95, $y+(76*$inTamY), 'COELBRA' );
        $this->Text( $x+156, $y+(76*$inTamY), '(71) 3621-7803' );
        $this->Line( $x+94, $y+(77*$inTamY), $x+184, $y+(77*$inTamY) );

        $this->Text( $x+95, $y+(80*$inTamY), 'EMBASA' );
        $this->Text( $x+156, $y+(80*$inTamY), '(71) 3635-1158' );
        $this->Line( $x+94, $y+(81*$inTamY), $x+184, $y+(81*$inTamY) );

        $this->Text( $x+95, $y+(84*$inTamY), 'RETRAN' );
        $this->Text( $x+156, $y+(84*$inTamY), '(71) 3635-1210' );

        //instrucoes de pagamento
        $this->setFont( 'Arial', 'B', 18 );
        $this->SetFillColor(240, 240, 240);
        $this->Rect( $x, $y+(100*$inTamY), 189, 12*$inTamY, 'DF');
        $this->Text( $x+26, $y+(108*$inTamY), 'I N S T R U Ç Õ E S    D E    P A G A M E N T O' );

        $this->setFont( 'Arial', 'B', 14 );
        $this->Text( $x+8, $y+(118*$inTamY), '"Pague o seu IPTU em dia e ajude a Prefeitura a promover mais educação, ');
        $this->Text( $x+8, $y+(124*$inTamY), 'mais saúde e outras ações, visando ao desenvolvimento do município".' );

        $this->setFont( 'Arial', '', 10 );
        $this->Text( $x+8, $y+(140*$inTamY), 'Prezado(a) Contribuinte,' );
        $this->Text( $x+8, $y+(144*$inTamY), 'O pagamento deste carnê não quita débitos anteriores. As parcelas vencidas estão sujeitas a multa e juros' );
        $this->Text( $x+8, $y+(147*$inTamY), 'moratórios. O prazo para reclamação do lançamento será de 30 (trinta) dias, contados a partir da data de' );
        $this->Text( $x+8, $y+(150*$inTamY), 'entrega do carnê. Os valores do demonstrativo estão expressos em Reais (R$). Caso o vencimento ocorra' );
        $this->Text( $x+8, $y+(153*$inTamY), 'em dia não útil, o pagamento deverá ser realizado no primeiro dia útil imediato. O IPTU não implica em reco-' );
        $this->Text( $x+8, $y+(156*$inTamY), 'nhecimento de direitos sobre o imóvel ou na sua regularização. Este carnê não poderá ser utilizado, para' );
        $this->Text( $x+8, $y+(159*$inTamY), 'pagamento, após o dia 31 de dezembro de 2008.' );

        $this->Rect( $x+8, $y+(165*$inTamY), 171, 10*$inTamY ); //lado b

        $this->setFont( 'Arial', 'B', 8 );
        $this->Text( $x+12, $y+(170*$inTamY), 'A legislação municipal pertinente aos tributos pode ser encontrada no site da Prefeitura Municipal de Mata de São João,' );
        $this->Text( $x+22, $y+(173*$inTamY), 'no endereço www.pmsj.ba.gov.br .' );

        $this->setFont( 'Arial', 'B', 10 );
        $this->Text( $x+26, $y+(179*$inTamY), 'PAGÁVEL EM QUALQUER AGÊNCIA DO BANCO DO BRASIL' );

        $this->setFont( 'Arial', 'B', 8 );
        $this->Text( $x+8, $y+(183*$inTamY), 'Qualquer dúvida, consulte o Setor de Tributos e Fiscalização, no Centro Administrativo sito à Rua Luiz Antônio Garcez,' );
        $this->Text( $x+8, $y+(186*$inTamY), 's/nº - Centro - CEP: 48.280-000 - Mata de São João/Ba. Tel/Fax: (71) 3635-1669 / (71) 3635-3294 / (71) 9617-7231.' );

        $this->addPage();
*/
        //pagina 3
        $this->stComposicaoCalculo = $stComposição;
        $this->SetXY($x+10,$y+5);
        /* Inicializa Fonte*/
        $this->setFont( 'Arial','',10 );

        /**
         * Retangulos
         */
        /* Retangulo da Composicação */
        $this->Rect( $x, $y, 189, 88*$inTamY );
        /* Retangulo Dados Cadastrais */
        $this->Rect( $x, $y + (100*$inTamY) , 189, 88*$inTamY );

        /* Composição do Calculo */
        $this->setFont( 'Arial','',8 );
        $this->setLeftMargin(10);
        $this->Write( 1.75 , $this->stComposicaoCalculo );
        $this->setLeftMargin(0);

        $y += 26;
        /**
         * Montar Estrutura dos Dados Cadastrais
         */

        /* Linhas Horizontais */
        $this->Line( $x , $y +  (90*$inTamY) , $x +189, $y +  (90*$inTamY) );
        $this->Line( $x , $y +  (98*$inTamY) , $x +189, $y +  (98*$inTamY) );
        $this->Line( $x , $y + (102*$inTamY) , $x +189, $y + (102*$inTamY) );
        $this->Line( $x , $y + (107*$inTamY) , $x +189, $y + (107*$inTamY) );
        $this->Line( $x , $y + (113*$inTamY) , $x +189, $y + (113*$inTamY) );
        $this->Line( $x , $y + (116*$inTamY) , $x +134, $y + (116*$inTamY) );
        $this->Line( $x , $y + (121*$inTamY) , $x +189, $y + (121*$inTamY) );
        $this->Line( $x , $y + (128*$inTamY) , $x +134, $y + (128*$inTamY) );
        $this->Line( $x , $y + (133*$inTamY) , $x +134, $y + (133*$inTamY) );
        $this->Line( $x , $y + (136*$inTamY) , $x +134, $y + (136*$inTamY) );
        $this->Line( $x , $y + (141*$inTamY) , $x +134, $y + (141*$inTamY) );
        $this->Line( $x+95 , $y + (147*$inTamY) , $x +134, $y + (147*$inTamY) );

        /* Linhas Verticais */

        /* Linha Ao lado do demonstrativo, aquela maior */
        $this->Line( $x + 134 , $y +  (113*$inTamY), $x + 134 , $y  + (165*$inTamY));

        /* Linha 1*/
        $this->Line( $x + 88 , $y +  (77.5*$inTamY) , $x + 88 , $y  +  (90*$inTamY));
        /* Linha 2*/
        $this->Line( $x +  64 , $y +  (90*$inTamY) , $x +  64 , $y  +  (98*$inTamY));
        $this->Line( $x +  91 , $y +  (90*$inTamY) , $x +  91 , $y  +  (98*$inTamY));
        $this->Line( $x + 136 , $y +  (90*$inTamY) , $x + 136 , $y  +  (98*$inTamY));
        /* Linha 3*/
        /* Linha 4*/
        $this->Line( $x +  34 , $y + (102*$inTamY) , $x + 34 , $y  + (107*$inTamY));
        $this->Line( $x + 92 , $y + (102*$inTamY) , $x + 92 , $y  + (107*$inTamY));
        $this->Line( $x + 136 , $y + (102*$inTamY) , $x + 136 , $y  + (107*$inTamY)); //linha do condominio

        /* Linha 5*/
        $this->Line( $x +  25  , $y + (107*$inTamY) , $x +  25 , $y  + (113*$inTamY));
        $this->Line( $x +  44  , $y + (107*$inTamY) , $x +  44 , $y  + (113*$inTamY));
        $this->Line( $x +  81  , $y + (107*$inTamY) , $x +  81 , $y  + (113*$inTamY));
        $this->Line( $x + 105  , $y + (107*$inTamY) , $x + 105 , $y  + (113*$inTamY));
        $this->Line( $x + 127  , $y + (107*$inTamY) , $x + 127 , $y  + (113*$inTamY));
        $this->Line( $x + 163  , $y + (107*$inTamY) , $x + 163 , $y  + (113*$inTamY));
        /* Linha 7*/
        $this->Line( $x +  25  , $y + (115*$inTamY) , $x +  25 , $y  + (121*$inTamY));
        $this->Line( $x +  64  , $y + (115*$inTamY) , $x +  64 , $y  + (121*$inTamY));
        $this->Line( $x +  76  , $y + (115*$inTamY) , $x +  76 , $y  + (121*$inTamY));
        $this->Line( $x + 108  , $y + (115*$inTamY) , $x + 108 , $y  + (121*$inTamY));
        /* Linha 8*/
        $this->Line( $x +  25  , $y + (121*$inTamY) , $x +  25 , $y  + (128*$inTamY));
        $this->Line( $x +  64  , $y + (121*$inTamY) , $x +  64 , $y  + (128*$inTamY));
        $this->Line( $x +  76  , $y + (121*$inTamY) , $x +  76 , $y  + (128*$inTamY));
        $this->Line( $x + 110  , $y + (121*$inTamY) , $x + 110 , $y  + (128*$inTamY));
        /* Linha 9*/
        $this->Line( $x +  25  , $y + (128*$inTamY) , $x +  25 , $y  + (133*$inTamY) );
        $this->Line( $x +  52  , $y + (128*$inTamY) , $x +  52 , $y  + (133*$inTamY) );
        $this->Line( $x +  98  , $y + (128*$inTamY) , $x +  98 , $y  + (133*$inTamY) );
        /* Linha 11*/
        $this->Line( $x +  21  , $y + (136*$inTamY) , $x +  21 , $y  + (141*$inTamY) );
        $this->Line( $x +  39  , $y + (136*$inTamY) , $x +  39 , $y  + (141*$inTamY) );
        $this->Line( $x +  56  , $y + (136*$inTamY) , $x +  56 , $y  + (141*$inTamY) );
        $this->Line( $x +  71  , $y + (136*$inTamY) , $x +  71 , $y  + (141*$inTamY) );
        $this->Line( $x +  92  , $y + (133*$inTamY) , $x +  92 , $y  + (141*$inTamY) );
        /* Linha 12 */
        $this->Line( $x +  95  , $y + (141*$inTamY) , $x +  95 , $y  + (147*$inTamY) );

        /**
         * Titulos dos Dados
         */
        /* imagem*/
        $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
        $this->Image( $this->stCamLogo , $x+1 , $y+(79*$inTamY) , 10 , 10 , $stExt);
        /* dados */
        $this->setFont( 'Arial','',14 );
        $this->Text( $x+112 , $y+(84*$inTamY) , 'DADOS CADASTRAIS' );
        /* exercicio */
        $this->setFont( 'Arial','',10 );
        $this->Text( $x+122 , $y+(88*$inTamY) , 'EXERCÍCIO DE '.Sessao::read( "exercicio" ) );

        $this->setFont( 'Arial','',5 );
        $this->Text( $x+  1 , $y+(92*$inTamY) , 'CONTRIBUINTE:' );
        $this->Text( $x+ 65 , $y+(92*$inTamY) , 'INSCRIÇÃO CADASTRAL:' );
        $this->Text( $x+ 92 , $y+(92*$inTamY) , 'CATEGORIA DE UTILIZAÇÃO DO IMÓVEL:' );
        $this->Text( $x+137 , $y+(92*$inTamY) , 'TIPO DE TRIBUTO(ESPECIFICAÇÃO):' );

        $this->setFont( 'Arial','',8 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(98*$inTamY) , 189 , 4*$inTamY , 'DF');
        $this->Text( $x+  72 , $y+(101*$inTamY) , 'E N D E R E Ç O  D O  I M Ó V E L' );

        $this->setFont( 'Arial','',5 );

        $this->Text( $x+    1 , $y+(104*$inTamY) , 'CÓDIGO DO LOGRADOURO:' );
        $this->Text( $x+   35 , $y+(104*$inTamY) , 'NOME DO LOGRADOURO:' );
        $this->Text( $x+  93 , $y+(104*$inTamY) , 'COMPLEMENTO:' );
        $this->Text( $x+  138 , $y+(104*$inTamY) , 'CONDOMÍNIO:' );

        $this->Text( $x+    1 , $y+(109*$inTamY) , 'QUADRA:' );
        $this->Text( $x+   26 , $y+(109*$inTamY) , 'LOTE:' );
        $this->Text( $x+   45 , $y+(109*$inTamY) , 'DISTRITO:' );
        $this->Text( $x+   82 , $y+(109*$inTamY) , 'REGIÃO:' );
        $this->Text( $x+  106 , $y+(109*$inTamY) , 'CEP:' );
        $this->Text( $x+  128 , $y+(109*$inTamY) , 'CIDADE:' );
        $this->Text( $x+  164 , $y+(109*$inTamY) , 'ESTADO:' );

        $this->setFont( 'Arial','',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(113*$inTamY) , 134 , 3*$inTamY , 'DF');
        $this->Text( $x+1 , $y+(115.2*$inTamY) , ' D A D O S   D O   I P T U   -   I M P O S T O   S O B R E   A   P R O P R I E D A D E   P R E D I A L   E   T E R R I T O R I A L   U R B A N A' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+    4 , $y+(118*$inTamY) , 'DADOS SOBRE O' );
        $this->Text( $x+    1 , $y+(120*$inTamY) , 'TERRENO OU ÁREA UTIL' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   26 , $y+(118*$inTamY) , 'ÁREA DE USO Privativo + Fração Ideal (M2):' );
        $this->Text( $x+   65 , $y+(118*$inTamY) , 'VUPt(R$/m2):' );
        $this->Text( $x+   78 , $y+(118*$inTamY) , 'VALOR VENAL DO TERRENO(R$):' );
        $this->Text( $x+  109 , $y+(118*$inTamY) , 'IMPOSTO TERRITORIAL(R$):' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+  135 , $y+(115*$inTamY) , 'DESCONTO PARA PAGAMENTO DE COTA ÚNICA:' );
        $this->Text( $x+  135 , $y+(117*$inTamY) , 'DO IPTU: 15,00%' );
        $this->Text( $x+  135 , $y+(119*$inTamY) , 'DA TAXA LIMPEZA PÚBLICA: 15,00%' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+    4 , $y+(123*$inTamY) , 'DADOS SOBRE A' );
        $this->Text( $x+    5 , $y+(125*$inTamY) , 'EDIFICAÇÃO' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   26 , $y+(123*$inTamY) , 'ÁREA DE USO Privativo + Fração Ideal (M2):' );
        $this->Text( $x+   65 , $y+(123*$inTamY) , 'VUPc(R$/m2):' );
        $this->Text( $x+   78 , $y+(123*$inTamY) , 'VALOR VENAL DA EDIFICAÇÃO(R$):' );
        $this->Text( $x+  111 , $y+(123*$inTamY) , 'IMPOSTO PREDIAL(R$):' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+  4.5 , $y+(130*$inTamY) , 'COMPOSIÇÃO' );
        $this->Text( $x+    5 , $y+(132*$inTamY) , 'DO IMPOSTO' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   26 , $y+(130*$inTamY) , 'ALIQUOTA:' );
        $this->Text( $x+   53 , $y+(130*$inTamY) , 'VALOR VENAL DO IMÓVEL:' );
        $this->Text( $x+   99 , $y+(130*$inTamY) , 'VALOR DO IMPOSTO:' );

        $this->setFont( 'Arial','',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(133*$inTamY) , 92 , 4 , 'DF');
        $this->Rect( $x+92, $y+(133*$inTamY) , 42 , 4 , 'DF');
        $this->Text( $x+18 , $y+(135*$inTamY) , ' D A D O S   D A   T A X A   D E   L I M P E Z A   P U B L I C A ' );
        $this->Text( $x+94 , $y+(135*$inTamY) , 'TAXA DE LIMPEZA PÚBLICA COMERCIAL' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   1 , $y+(138*$inTamY) , 'TIPO DE UNIDADE:' );
        $this->Text( $x+  22 , $y+(138*$inTamY) , 'ZONA:' );
        $this->Text( $x+  40 , $y+(138*$inTamY) , 'ÁREA (M2):' );
        $this->Text( $x+  57 , $y+(138*$inTamY) , 'VALOR (R$/M2):' );
        $this->Text( $x+  72 , $y+(138*$inTamY) , 'VALOR DA TAXA(R$):' );

        $this->setFont( 'Arial','B',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x+134, $y+(121*$inTamY) , 55 , 3*$inTamY , 'DF');
        $this->Text( $x+148 , $y+(123*$inTamY) , ' DEMONSTRATIVO DAS PARCELAS' );

        $this->setFont( 'Arial','B',7 );
        $this->Text( $x+141 , $y+(128*$inTamY) , ' Cota única: ' . $this->arVencimentosDemonstrativos[0] . ', R$ ' . $this->arDemonstrativoParcelas[0]);

        $this->setFont( 'Arial','B',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x+134, $y+(131*$inTamY) , 55 , 3*$inTamY , 'DF');
        $this->Text( $x+148 , $y+(133*$inTamY) , ' PARCELAS:' );

        /* parcelas */
        $this->setFont( 'Arial', '', 5 );
        $this->Text( $x+135 , $y+(139*$inTamY) , '01) ' . $this->arVencimentosDemonstrativos[1] . ', R$' . $this->arDemonstrativoParcelas[1] );
        $this->Text( $x+135 , $y+(144*$inTamY) , '02) ' . $this->arVencimentosDemonstrativos[2] . ', R$' . $this->arDemonstrativoParcelas[2] );
        $this->Text( $x+135 , $y+(149*$inTamY) , '03) ' . $this->arVencimentosDemonstrativos[3] . ', R$' . $this->arDemonstrativoParcelas[3] );
        $this->Text( $x+135 , $y+(154*$inTamY) , '04) ' . $this->arVencimentosDemonstrativos[4] . ', R$' . $this->arDemonstrativoParcelas[4] );
        $this->Text( $x+135 , $y+(159*$inTamY) , '05) ' . $this->arVencimentosDemonstrativos[5] . ', R$' . $this->arDemonstrativoParcelas[5] );

        $this->Text( $x+135 , $y+(164*$inTamY) , '06) ' . $this->arVencimentosDemonstrativos[6] . ', R$' . $this->arDemonstrativoParcelas[6] );

        $this->Text( $x+162 , $y+(139*$inTamY) , '07) ' . $this->arVencimentosDemonstrativos[7] . ', R$' . $this->arDemonstrativoParcelas[7] );
        $this->Text( $x+162 , $y+(144*$inTamY) , '08) ' . $this->arVencimentosDemonstrativos[8] . ', R$' . $this->arDemonstrativoParcelas[8] );
        $this->Text( $x+162 , $y+(149*$inTamY) , '09) ' . $this->arVencimentosDemonstrativos[9] . ', R$' . $this->arDemonstrativoParcelas[9] );
        $this->Text( $x+162 , $y+(154*$inTamY) , '10) ' . $this->arVencimentosDemonstrativos[10] . ', R$' . $this->arDemonstrativoParcelas[10] );

        $this->Text( $x+162 , $y+(159*$inTamY) , '11) ' . $this->arVencimentosDemonstrativos[11] . ', R$' . $this->arDemonstrativoParcelas[11] );

        //$this->Text( $x+161 , $y+(165*$inTamY) , '12) ' . $this->arVencimentosDemonstrativos[12] . ', R$' . $this->arDemonstrativoParcelas[12] );

        $this->setFont( 'Arial','B',6 );
        $this->Text( $x+96 , $y+(143*$inTamY) , 'VALOR TOTAL DOS TRIBUTOS (R$):' );

        $this->setFont( 'Arial','',6 );
        $this->Text( $x+3 , $y+(158*$inTamY) , 'A identificação dos campos e a forma de cálculo dos tributos constam nas folhas anteriores. Qualquer dúvida, consulte o Setor de ' );
        $this->Text( $x+6 , $y+(161*$inTamY) , 'Tributos e Fiscalização, no Centro Administrativo, sito à Rua Luiz Antônio Garcez, s/nº - Centro - CEP: 48.280-000 - Mata de' );
        $this->Text( $x+6 , $y+(164*$inTamY) , 'São João/Ba. Tel/Fax: (71) 3635-1669 / (71) 3635-3294 / (71) 9617-7231.' );
        /* mostrar dados */

        $this->setFont( 'Arial','B',7 );
        $this->Text( $x+14 , $y+(85*$inTamY) , $this->stNomePrefeitura );
        $this->setFont( 'Arial','',6 );
        $this->Text( $x+14 , $y+(88*$inTamY) , $this->stSubTitulo );

        $this->setFont( 'Arial','',5 );
        /* contribuibte */
        $this->Text( $x+ 2 , $y+(96*$inTamY) , $this->stContribuinte );
        /* inscricao */
        $this->Text( $x+74 , $y+(96*$inTamY) , $this->stInscricaoCadastral );
        /* categoria */
        $this->Text( $x+107 , $y+(96*$inTamY) , $this->stCategoriaUtilizacao );
        /* tipo tributo */
        $this->Text( $x+138 , $y+(96*$inTamY) , $this->stTipoTributo );

        /* logradouro */
        $this->Text( $x+14 , $y+(106*$inTamY) , $this->stCodigoLogradouro );
        /* nome logradouro */
        $this->Text( $x+35 , $y+(106*$inTamY) , $this->stNomeLogradouro );

        $this->Text( $x+94 , $y+(106*$inTamY) , $this->stComplemento );

        $this->Text( $x+140 , $y+(106*$inTamY) , $this->stCondominio );

        /* quadra*/
        $this->Text( $x+ 14 , $y+(112*$inTamY) , $this->stQuadra );
        /* lote */
        $this->Text( $x+ 29 , $y+(112*$inTamY) , $this->stLote);
        /* distrito */
        $this->Text( $x+ 45 , $y+(112*$inTamY) , $this->stDistrito );
        /* regiao */
        $this->Text( $x+ 89 , $y+(112*$inTamY) , $this->stRegiao);
        /* cep */
        $this->Text( $x+108 , $y+(112*$inTamY) , $this->stCep);
        /* cidade */
        $this->Text( $x+129 , $y+(112*$inTamY) , $this->stCidade );
        /* estado */
        $this->Text( $x+170 , $y+(112*$inTamY) , $this->stEstado );

        /* uso privativo terreno */
        $this->Text( $x+ 43 , $y+(120*$inTamY) , $this->stAreaUsoPrivativoTerreno );
        /* vupt */
        $this->Text( $x+ 67 , $y+(120*$inTamY) , $this->stVupt);
        /* venal terreno */
        $this->Text( $x+ 89 , $y+(120*$inTamY) , $this->stValorVenalTerreno );
        /* imposto terre */
        $this->Text( $x+ 115, $y+(120*$inTamY) , $this->stImpostoTerritorial);
        /* uso privativo edificaoca */
        $this->Text( $x+ 43 , $y+(126*$inTamY) , $this->stAreaUsoPrivativoEdificacao);
        /* vupc */
        $this->Text( $x+ 67 , $y+(126*$inTamY) , $this->stVupc );
        /* venal constru */
        $this->Text( $x+ 89 , $y+(126*$inTamY) , $this->stValorVenalConstrucao );
        /* impostto predial */
        $this->Text( $x+115 , $y+(126*$inTamY) , $this->stImpostoPredial );

        /* aliquota */
        $this->Text( $x+ 36 , $y+(132*$inTamY) , $this->stAliquota );
        /* venal imovel */
        $this->Text( $x+ 71, $y+(132*$inTamY) , $this->stValorVenalImovel);
        /* imposto */
        $this->Text( $x+ 111 , $y+(132*$inTamY) , $this->stValorImposto);

        /* tipo */
        $this->Text( $x+ 5 , $y+(139.5*$inTamY) , $this->stTipoUnidade );
        /* zona */
        $this->Text( $x+ 23 , $y+(139.5*$inTamY) , $this->stZona );
        /* area */
        $this->Text( $x+ 42 , $y+(139.5*$inTamY) , $this->stAreaM2 );
        /* balor area*/
        $this->Text( $x+ 58 , $y+(139.5*$inTamY) , $this->stValorM2);
        /* taxa */
        $this->Text( $x+ 75 , $y+(139.5*$inTamY) , $this->stValorTaxa );
        /* ilu*/
        $this->Text( $x+ 108 , $y+(139*$inTamY) , $this->stContribIlumPublica );

        /* total */
        $this->setFont( 'Arial','B',7 );
        $this->Text( $x+ 110 , $y+(145.5*$inTamY) , $this->stValorTotalTributos );

        $this->setFont('Arial','',4);
        $this->Text   ( $x+2, $y+(167*$inTamY), 'Data de emissão: '.date("d/m/Y h:i:s"));
    }
//  function novaPagina() {
//      $this->addPage();
//  }
//
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneIPTUComplementarMataSaoJoao2008
{
/*
    * @var Integer
    * @access Private
*/
var $inHorizontal;
/*
    * @var Integer
    * @access Private
*/
var $inVertical;
/*
    * @var Array
    * @access Private
*/
var $arEmissao;
/*
    * @var Object
    * @access Private
*/
var $obBarra;
/*
    * @var Array
    * @access Private
*/
var $arBarra;
/*
    * @var Boolean;
    * @access Private
*/
var $boPulaPagina;
/*
    * @var Object
    * @access Private
*/
var $obRARRCarne;
var $obRCarneDadosCadastrais;
var $stLocal;
var $boConsolidacao;
var $stNumeracaoConsolidacao;
var $dtVencimentoConsolidacao;

/* setters */
function setHorizontal($valor) { $this->inHorizontal = $valor; }
function setVertical($valor) { $this->inVertical   = $valor; }
function setEmissao($valor) { $this->arEmissao    = $valor; }
function setBarra($valor) { $this->obBarra      = $valor; }
function setArBarra($valor) { $this->arBarra      = $valor; }
function setPulaPagina($valor) { $this->boPulaPagina = $valor; }
function setConsolidacao($valor) { $this->boConsolidacao = $valor;            }
function setVencimentoConsolidacao($valor) { $this->dtVencimentoConsolidacao = $valor;  }
function setNumeracaoConsolidacao($valor) { $this->stNumeracaoConsolidacao = $valor;   }

/* getters */
function getHorizontal() { return $this->inHorizontal;   }
function getVertical() { return $this->inVertical;     }
function getEmissao() { return $this->arEmissao;      }
function getBarra() { return $this->obBarra;        }
function getArBarra() { return $this->arBarra;        }
function getPulaPagina() { return $this->boPulaPagina;   }
function getConsolidacao() { return $this->boConsolidacao;   }
function getNumeracaoConsolidacao() { return $this->stNumeracaoConsolidacao; }
function getVencimentoConsolidacao() { return $this->dtVencimentoConsolidacao; }

/*
    * Metodo Construtor
    * @access Private
*/
function RCarneIPTUComplementarMataSaoJoao2008($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
    $this->boConsolidacao = false;
    //$obRProtocolo = new RProtocolo;
    //$obRCarnePetropolis     = new RCarnePetropolis;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;
    //---------------------
    $this->obRARRConfiguracao     = new RARRConfiguracao;
    $this->obRARRConfiguracao->setCodModulo ( 2 );
    $this->obRARRConfiguracao->consultar();
    $inCodFebraban = $this->obRARRConfiguracao->getCodFebraban();
    unset($this->obRARRConfiguracao);

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
    $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );
    $stNomeImagem = $rsListaImagens->getCampo("valor");

    $inSaltaPagina = "";
    $this->obRCarnePetropolis = new RCarneDadosCadastraisMataSaoJoao();
    $this->obRCarnePetropolis->stCamLogo = CAM_FW_TEMAS."imagens/".$stNomeImagem;
    $this->obRCarnePetropolis->lblTitulo1 = "MATA DE SÃO JOÃO - Sec. de Adm. e Fin.";

    //$this->obRCarnePetropolis->configuraCarne();
    $nuValorTotal = $nuValorNormal = $nuValorJuroNormal = $nuValorMultaNormal = 0.00;
    //$this->obRCarnePetropolis->configuraProtocolo();
    foreach ($this->arEmissao as $valor => $chave) {
        /* imprimir duas folhas com dados cadastrais */
        /* buscar informações para dados cadastrais*/

        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
        $stFiltro = " WHERE aivv.inscricao_municipal = ". $chave[0]['inscricao'] ." AND aivv.exercicio = ".$chave[0]['exercicio']." \n";

        $obTARRCarne = new TARRCarne;
        $obTARRCarne->recuperaDadosIPTUComplementarMata( $rsListaCarne, $stFiltro, $chave[0]['cod_parcela'] );

        $rsListaCarne->addFormatacao ('area_lote','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('vupt','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('vupc','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_territorial_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('imposto_territorial','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('imposto_predial','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_imovel','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_predial_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('aliquota','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_total_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_imposto','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_m2_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_m2_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('taxa_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_total_tributos','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('taxa_luz','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_parcela','NUMERIC_BR');

        $arDadosParcelas = array();
        $inTotalParcelas = 0;
        if ( !$rsListaCarne->Eof() ) {
            $rsListaCarne->setPrimeiroElemento();
            //$inCodCalculo = $rsListaCarne->getCampo("cod_calculo");
            while ( !$rsListaCarne->Eof() ) {
                if ( $rsListaCarne->getCampo("nro_parcela") != "única" ) {
                    $arDadosParcelas[$rsListaCarne->getCampo("nro_parcela")]["data"] = $rsListaCarne->getCampo("vencimento_parcela");

                    $arDadosParcelas[$rsListaCarne->getCampo("nro_parcela")]["valor"] = $rsListaCarne->getCampo("valor_parcela");
                } else {
                    $arDadosParcelas[0]["data"] = $rsListaCarne->getCampo("vencimento_parcela");
                    $arDadosParcelas[0]["valor"] = $rsListaCarne->getCampo("valor_parcela");
                }

                $inTotalParcelas++;
                if ( $inTotalParcelas > 11 )
                    break;

                $rsListaCarne->proximo();
            }

        }

        $rsListaCarne->setPrimeiroElemento();

        /* setar todos os dados necessarios */
        $this->obRCarnePetropolis->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->obRCarnePetropolis->stSubTitulo = 'Secretaria de Administração e Finanças';
        $this->obRCarnePetropolis->stExercicio  = (string) $rsListaCarne->getCampo("exercicio"); //'2006';
        $this->obRCarnePetropolis->stContribuinte  = (string) $rsListaCarne->getCampo("nom_proprietario"); //'WELLIGNTON LAZARO BARRETO DE OLIVEIRA' ;
        $this->obRCarnePetropolis->stInscricaoCadastral  = (string) $rsListaCarne->getCampo("inscricao_municipal"); //'015041' ;
        $this->obRCarnePetropolis->stCategoriaUtilizacao  = (string) $rsListaCarne->getCampo("categoria_utilizacao_imovel"); //'RESIDENCIAL' ;
        $this->obRCarnePetropolis->stTipoTributo  = 'IPTU / TAXA DE LIMPEZA / CONTRB.  DE ILUM. PÚBLICA' ;
        $this->obRCarnePetropolis->stCodigoLogradouro  = (string) $rsListaCarne->getCampo("cod_logradouro"); //'50.003' ;

        $this->obRCarnePetropolis->stNomeLogradouro  = (string) str_replace ( "Não Informado ", "", $rsListaCarne->getCampo("endereco_logradouro") ); //'AV DO FAROL 50.003' ;
        $this->obRCarnePetropolis->stComplemento  = (string) $rsListaCarne->getCampo("endereco_complemento"); //'CONDOMINIO SOLAR DOS ARCOS' ;
        $this->obRCarnePetropolis->stQuadra  = (string) $rsListaCarne->getCampo("numero_quadra"); //'02' ;
        $this->obRCarnePetropolis->stLote  = (string) $rsListaCarne->getCampo("numero_lote"); //'02' ;
        $this->obRCarnePetropolis->stDistrito  = (string) $rsListaCarne->getCampo("distrito"); //'PRAIA DO FORTE' ;
        $this->obRCarnePetropolis->stCondominio = (string) $rsListaCarne->getCampo("condominio");
        $this->obRCarnePetropolis->stRegiao  = (string) $rsListaCarne->getCampo("regiao"); //'LITORAL' ;
        $this->obRCarnePetropolis->stCep  = (string) $rsListaCarne->getCampo("cep"); //'48.820-000' ;
        $this->obRCarnePetropolis->stCidade  = 'MATA DE SÃO JOÃO' ;
        $this->obRCarnePetropolis->stEstado  = 'BAHIA' ;
        $this->obRCarnePetropolis->stAreaUsoPrivativoTerreno  = (string) $rsListaCarne->getCampo("area_lote"); //'114,52' ;
        $this->obRCarnePetropolis->stVupt = (string) $rsListaCarne->getCampo("vupt"); //'4,5' ;
        $this->obRCarnePetropolis->stVupc = (string) $rsListaCarne->getCampo("vupc"); //'180,00' ;
        $this->obRCarnePetropolis->stValorVenalTerreno = (string) $rsListaCarne->getCampo("venal_territorial_calculado"); //'526,50' ;
        $this->obRCarnePetropolis->stImpostoTerritorial = (string) $rsListaCarne->getCampo("imposto_territorial"); //'5,27' ;
        $this->obRCarnePetropolis->stAreaUsoPrivativoEdificacao = (string) $rsListaCarne->getCampo("area_imovel"); //'50,00' ;
        $this->obRCarnePetropolis->stValorVenalConstrucao = (string) $rsListaCarne->getCampo("venal_predial_calculado"); //'10.800,00' ;
        $this->obRCarnePetropolis->stImpostoPredial = (string) $rsListaCarne->getCampo("imposto_predial"); //'108,00' ;
        $this->obRCarnePetropolis->stAliquota = (string) $rsListaCarne->getCampo("aliquota"); //'1,00 %' ;
        $this->obRCarnePetropolis->stValorVenalImovel = (string) $rsListaCarne->getCampo("venal_total_calculado"); //'11.326,50' ;
        $this->obRCarnePetropolis->stValorImposto = (string) $rsListaCarne->getCampo("valor_imposto"); //'113,27' ;
        $this->obRCarnePetropolis->stTipoUnidade = (string) $rsListaCarne->getCampo("categoria_utilizacao_imovel"); //'RESIDENCIAL';
        $this->obRCarnePetropolis->stZona = (string) $rsListaCarne->getCampo("zona"); //'POPULAR' ;
        $this->obRCarnePetropolis->stAreaM2 = (string) $rsListaCarne->getCampo("area_m2_limpeza_publica"); //'11.656.220,00' ;
        $this->obRCarnePetropolis->stValorM2 = (string) $rsListaCarne->getCampo("valor_m2_limpeza_publica"); //'1,01' ;
        $this->obRCarnePetropolis->stValorTaxa = (string) $rsListaCarne->getCampo("taxa_limpeza_publica"); //'28,93' ;
        $this->obRCarnePetropolis->stValorTotalTributos = (string) $rsListaCarne->getCampo("valor_total_tributos"); //'162,20' ;
        $this->obRCarnePetropolis->stContribIlumPublica = (string) $rsListaCarne->getCampo("taxa_luz"); //'20,00';
        $this->obRCarnePetropolis->arDemonstrativoParcelas = array 	(
                                                         0 => $arDadosParcelas[0]["valor"], //'140,87',
                                                         1 => $arDadosParcelas[1]["valor"], //'16,22' ,
                                                         2 => $arDadosParcelas[2]["valor"], //'16,22' ,
                                                         3 => $arDadosParcelas[3]["valor"], //'16,22' ,
                                                         4 => $arDadosParcelas[4]["valor"], //'16,22' ,
                                                         5 => $arDadosParcelas[5]["valor"], //'16,22' ,
                                                         6 => $arDadosParcelas[6]["valor"], //'16,22' ,
                                                         7 => $arDadosParcelas[7]["valor"], //'16,22' ,
                                                         8 => $arDadosParcelas[8]["valor"], //'16,22' ,
                                                         9 => $arDadosParcelas[9]["valor"], //'16,22' ,
                                                         10 => $arDadosParcelas[10]["valor"], //'16,22'
                                                         11 => $arDadosParcelas[11]["valor"], //'16,22'
                                                         12 => $arDadosParcelas[12]["valor"] //'16,22'
                                                    ) ;
        $this->obRCarnePetropolis->arVencimentosDemonstrativos = array 	(
                                                         0 => $arDadosParcelas[0]["data"], //'05/02/2007',
                                                         1 => $arDadosParcelas[1]["data"], //'05/02/2007' ,
                                                         2 => $arDadosParcelas[2]["data"], //'05/03/2007' ,
                                                         3 => $arDadosParcelas[3]["data"], //'05/04/2007' ,
                                                         4 => $arDadosParcelas[4]["data"], //'05/05/2007' ,
                                                         5 => $arDadosParcelas[5]["data"], //'05/06/2007' ,
                                                         6 => $arDadosParcelas[6]["data"], //'05/07/2007' ,
                                                         7 => $arDadosParcelas[7]["data"], //'05/08/2007' ,
                                                         8 => $arDadosParcelas[8]["data"], //'05/09/2007' ,
                                                         9 => $arDadosParcelas[9]["data"], //'05/10/2007' ,
                                                         10 => $arDadosParcelas[10]["data"], //'05/11/2007'
                                                         11 => $arDadosParcelas[11]["data"], //'05/11/2007'
                                                         12 => $arDadosParcelas[12]["data"] //'05/11/2007'
                                                    ) ;

        $this->obRCarnePetropolis->desenhaCarne(10,20);
        $this->obRCarnePetropolis->novaPagina();

        $inSaltaPagina++;

        $this->obRCarnePetropolis->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem ); //logoCarne.png" );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarneDiverso( $rsGeraCarneCabecalho );
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarnePetropolis->setObservacaoL1 ('Créditos: ');

        while ( !$rsGeraCarneCabecalho->eof() ) {
            /* montagem cabecalho (protocolo) */
            $this->obRCarnePetropolis->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarnePetropolis->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarnePetropolis->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarnePetropolis->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarnePetropolis->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );
            $this->obRCarnePetropolis->setRua               ( str_replace ( "Não Informado ", "",  $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) ) );
            $this->obRCarnePetropolis->setNumero            ( $rsGeraCarneCabecalho->getCampo( 'numero' )                 );
            $this->obRCarnePetropolis->setComplemento       ( $rsGeraCarneCabecalho->getCampo( 'complemento' )            );
            $this->obRCarnePetropolis->setCidade            ( $rsGeraCarneCabecalho->getCampo( 'nom_municipio' )          );
            $this->obRCarnePetropolis->setUf                ( $rsGeraCarneCabecalho->getCampo( 'sigla_uf' )               );
            $this->obRCarnePetropolis->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ),strlen( $stMascaraInscricao ), '0', STR_PAD_LEFT) );
            $this->obRCarnePetropolis->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarnePetropolis->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarnePetropolis->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarnePetropolis->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarnePetropolis->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarnePetropolis->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarnePetropolis->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
            $this->obRCarnePetropolis->setTributo           ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' )              );
            $this->obRCarnePetropolis->setValorTributoReal  ( $rsGeraCarneCabecalho->getCampo( 'valor_venal_total' )      );
            $this->obRCarnePetropolis->setObservacao        ( wordwrap($rsGeraCarneCabecalho->getCampo('observacao' ),40,chr(13)) );
            $this->obRCarnePetropolis->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
            $this->obRCarnePetropolis->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );
            if ( preg_match( '/LIMPEZA.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( $rsGeraCarneCabecalho->getCampo( 'valor' )              );
            } else {
                $flImpostoAnualReal = $rsGeraCarneCabecalho->getCampo( 'valor' );
                $this->obRCarnePetropolis->setImpostoAnualReal  ( $flImpostoAnualReal                                     );
            }
            $this->obRCarnePetropolis->setReferencia        ( ""                                                          );
            $this->obRCarnePetropolis->setNumeroPlanta      ( ""                                                          );

            // capturar creditos
            $this->obRCarnePetropolis->setObservacaoL1 ( $this->obRCarnePetropolis->getObservacaoL1().$rsGeraCarneCabecalho->getCampo( 'descricao_credito').": ".$rsGeraCarneCabecalho->getCampo( 'valor' )."  ");

            $rsGeraCarneCabecalho->proximo();

        } //fim do loop de reemitirCarne
        $this->obRCarnePetropolis->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarnePetropolis->getTaxaLimpezaAnual() );
        // formatar
        $this->obRCarnePetropolis->setValorAnualReal    ( number_format($this->obRCarnePetropolis->getValorAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( number_format($this->obRCarnePetropolis->getTaxaLimpezaAnual(),2,',','.') );
        $this->obRCarnePetropolis->setImpostoAnualReal  ( number_format($this->obRCarnePetropolis->getImpostoAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setValorTributoReal  ( number_format($this->obRCarnePetropolis->getValorTributoReal(),2,',','.') );
/*        $this->obRCarnePetropolis->drawProtocolo();
        $this->obRCarnePetropolis->posicionaVariaveisProtocolo();
*/
        $inParcela = $inCount = "";

        $this->inHorizontal = 7;
        $this->inVertical = 20;

        $this->obBarra = new RCodigoBarraFebrabanCompensacaoBB_Anexo5;
        $this->arBarra = array();

        /*********************** CONSOLIDACAO */
        if ( $this->getConsolidacao() ) {

        #echo '<h2>CONSOLIDACAO </h2>'; exit;


            foreach ($chave as $parcela) {

                $inParcela++;

                $this->obRCarnePetropolis->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
                $this->obRCarnePetropolis->setImagem("");
                $this->obRARRCarne->obRARRParcela->setCodParcela( $parcela["cod_parcela"] );
                $obErro = $this->obRARRCarne->obRARRParcela->listarParcelaCarne( $rsParcela );

                // instanciar mapeamento da função de calculo de juro e multa
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaParcelasReemissao.class.php');
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaJuroOrMultaParcelasReemissao.class.php');
                // retorna parcela com juro e multa aplicados
                $obCalculaParcelas = new FARRCalculaParcelasReemissao;
                // retorna valores de juro e multa que foram aplicados
                $obCalculaJM = new FARRCalculaJuroOrMultaParcelasReemissao;





                $dtVencimento = $this->getVencimentoConsolidacao();
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );

                // data da reemissao
                #$arTmp = explode ( '/', $rsParcela->getCampo( 'vencimento' ) );
                #$dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                // parametro padrao
                #$stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                #$stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                #$stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                #$obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro1);
                #$nuValorNormal = $rsTmp->getCampo('valor');


                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['convenio'] = 960663;
                $this->arBarra['tipo_moeda'] = 9;
                if ( !$obErro->ocorreu() ) {

                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_parcela"    , $rsParcela->getCampo('cod_parcela')   );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_lancamento" , $rsParcela->getCampo('cod_lancamento'));
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "nr_parcela"     , $rsParcela->getCampo('nr_parcela')    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "vencimento"     , $this->getVencimentoConsolidacao()    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "valor" , $nuValorTotal );
                    $obErro = $this->obRARRCarne->obRARRParcela->obTARRParcela->alteracao($boTransacao);

                    #$this->obRARRCarne->obRARRParcela->obTARRParcela->debug();
                    #exit;
                }

                $nuValorTotal += $arValorNormal[0];
                $nuValorNormal += $arValorNormal[1];
                $nuValorJuroNormal += $arValorNormal[3];
                $nuValorMultaNormal += $arValorNormal[2];
                $nuValorCorrecaoNormal += $arValorNormal[4];
            }


                $this->obRCarnePetropolis->setObservacaoL1 ( 'Não receber após o vencimento. ' );
                $this->obRCarnePetropolis->setParcela ( "1/1" );
                $this->obRCarnePetropolis->setVencimento  ( $this->getVencimentoConsolidacao() );
                $this->obRCarnePetropolis->flValorJuros = ( number_format(round($nuValorJuroNormal,2),2,',',''));
                $this->obRCarnePetropolis->flValorMulta = ( number_format(round($nuValorMultaNormal,2),2,',',''));
                $this->obRCarnePetropolis->flValorOutros = ( number_format(round($nuValorCorrecaoNormal,2),2,',',''));
                $this->obRCarnePetropolis->setValor       ( number_format(round($nuValorNormal,2),2,',',''));
                $this->obRCarnePetropolis->setValorTotal(number_format(round($nuValorTotal,2),2,',',''));

                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

                $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 100;


        } else {

            foreach ($chave as $parcela) { // impressao das parcelas selecionadas para cada codigo de lancamento
                $inParcela++;

                $this->obRCarnePetropolis->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem ); //logoCarne.png" ); //imagem mudar
                $this->obRCarnePetropolis->setImagem("");
                $this->obRARRCarne->obRARRParcela->setCodParcela( $parcela["cod_parcela"] );
                $obErro = $this->obRARRCarne->obRARRParcela->listarParcelaCarne( $rsParcela );

                // instanciar mapeamento da função de calculo de juro e multa
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaParcelasReemissao.class.php');
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaJuroOrMultaParcelasReemissao.class.php');
                // retorna parcela com juro e multa aplicados
                $obCalculaParcelas = new FARRCalculaParcelasReemissao;
                // retorna valores de juro e multa que foram aplicados
                $obCalculaJM = new FARRCalculaJuroOrMultaParcelasReemissao;

                // data da reemissao
                $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
                $dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                // parametro padrao
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );
                $nuValorTotal = $arValorNormal[0];
                $nuValorNormal = $arValorNormal[1];
                $stJuroNormal = $arValorNormal[3];
                $stMultaNormal = $arValorNormal[2];
                $stCorrecaoNormal = $arValorNormal[5];

                $this->arBarra['valor_documento'] = $nuValorTotal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['convenio'] = 960663;
                $this->arBarra['tipo_moeda'] = 9;

                if ( $obErro->ocorreu() ) {
                    break;
                }
                if ($diffBaixa) {
                    $this->obRCarnePetropolis->setParcelaUnica ( true );
                    $this->obRCarnePetropolis->lblTitulo2        = ' ';
                    $this->obRCarnePetropolis->lblValorCotaUnica = 'VALOR TOTAL';
                    $this->obRCarnePetropolis->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                    $this->obRCarnePetropolis->setValor        ( number_format($nuValorNormal,2,',','.') );
                    $this->obRCarnePetropolis->setParcela ( $rsParcela->getCampo( 'info' ) );
                } else {
                    if ( $rsParcela->getCampo( 'nr_parcela' ) == 0 ) {
                        $this->obRCarnePetropolis->setParcelaUnica ( true );
                        $this->obRCarnePetropolis->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                        $this->obRCarnePetropolis->setValor        ( number_format($nuValorNormal,2,',','.') );
                        // Recuperar Desconto
                        include_once(CAM_GT_ARR_MAPEAMENTO."FARRParcentualDescontoParcela.class.php");
                        $obPercentual = new FARRParcentualDescontoParcela;
                        $obPercentual->executaFuncao($rsPercentual,"".$parcela["cod_parcela"].",'".$dtVencimento."'");
                        $this->obRCarnePetropolis->setObservacaoL1 ( 'Cota Única com '.$rsPercentual->getCampo('valor').'% de desconto.' );                      ;
                        $this->obRCarnePetropolis->setObservacaoL2 ( 'Desconto não incide sobre a Taxa de Coleta de Lixo' );
                        $this->obRCarnePetropolis->setObservacaoL3 ( 'Não receber após o vencimento.' );
                        $this->obRCarnePetropolis->stObsVencimento = "Não receber após o vencimento.";
                        $this->obRCarnePetropolis->setParcela ( 'ÚNICA' );
                    } else {
                        $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ),4);
                        $this->obRCarnePetropolis->setParcela( $rsParcela->getCampo( 'info' ));
                        $this->obRCarnePetropolis->setParcelaUnica( false );
                        $this->obRCarnePetropolis->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );

                        $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
                        $boVenc1 = false;
                        $boVenc2 = false;
                        $boVenc3 = false;

                        if ($this->stLocal != "WEB") {
                            $stMes = $arTmp[1];
                            $arTmp = explode('/',$arVencimentos[0]);
                            if ($arTmp[1] >= $stMes) {
                                $stMes = $arTmp[1];
                                $boVenc1 = true;
                                $this->obRCarnePetropolis->setVencimento1 ( $arVencimentos[0] );
                                $arTmp = explode('/',$arVencimentos[1]);
                                if ($arTmp[1] >= $stMes) {
                                    $stMes = $arTmp[1];
                                    $boVenc2 = true;
                                    $this->obRCarnePetropolis->setVencimento2 ( $arVencimentos[1] );
                                    $arTmp = explode('/',$arVencimentos[2]);
                                    if ($arTmp[1] >= $stMes) {
                                        $boVenc3 = true;
                                        $this->obRCarnePetropolis->setVencimento3 ( $arVencimentos[2] );
                                    }
                                }
                            }
                            // converter vencimentos para formato americano

                            $arTmp = explode('/',$arVencimentos[0]);
                            $dtVencimento1 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            $arTmp = explode('/',$arVencimentos[1]);
                            $dtVencimento2 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            $arTmp = explode('/',$arVencimentos[2]);
                            $dtVencimento3 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            $stParametro2 = $stParametro.$dtVencimento1."'";
                            $stParametro3 = $stParametro.$dtVencimento2."'";
                            $stParametro4 = $stParametro.$dtVencimento3."'";

                            // valor, % de juro, % de multa para valor normal do carne --------------
                            // valor
                            // % de juro
                            //$obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'j'");
                            //$stJuroNormal = $rsTmp->getCampo('valor');

                            $this->obRCarnePetropolis->flValorJuros = $stJuroNormal;

                            // % de multa
    //                        $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'m'");
    //                        $stMultaNormal = $rsTmp->getCampo('valor');
                            $this->obRCarnePetropolis->flValorMulta = $stMultaNormal;
                            $this->obRCarnePetropolis->flValorOutros = $stCorrecaoNormal;
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 1 do carne --------------
                            // valor
                            if ($boVenc1 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro2);
                                $nuValor1 = $rsTmp->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'j'");
                                $stJuro1 = $rsTmp->getCampo('valor');
                                $this->obRCarnePetropolis->lblJuros2 = $stJuro1;
                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'m'");

                                $stMulta1 = $rsTmp->getCampo('valor');
                                $this->obRCarnePetropolis->lblMulta2 = $stMulta1;
                            } else {
                                $this->obRCarnePetropolis->lblJuros2 = "";
                                $this->obRCarnePetropolis->lblMulta2 = "";
                            }
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 2 do carne --------------
                            // valor
                            if ($boVenc2 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro3);
                                $nuValor2 = $rsTmp1->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro3.",'j'");

                                $stJuro2 = $rsTmp2->getCampo('valor');

                                $this->obRCarnePetropolis->lblJuros3 = $stJuro2;

                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro3.",'m'");
                                $stMulta2 = $rsTmp3->getCampo('valor');
                                $this->obRCarnePetropolis->lblMulta3 = $stMulta2;
                            } else {
                                $this->obRCarnePetropolis->lblJuros3 = "";
                                $this->obRCarnePetropolis->lblMulta3 = "";
                            }
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 3 do carne --------------
                            // valor
                            if ($boVenc3 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro4);
                                $nuValor3 = $rsTmp1->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro4.",'j'");

                                $stJuro3 = $rsTmp2->getCampo('valor');
                                $this->obRCarnePetropolis->lblJuros4 = $stJuro3;
                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro4.",'m'");
                                $stMulta3 = $rsTmp3->getCampo('valor');

                                $this->obRCarnePetropolis->lblMulta4 = $stMulta3;
                            } else {
                                $this->obRCarnePetropolis->lblJuros4 = "";
                                $this->obRCarnePetropolis->lblMulta4 = "";
                            }
                            //-----------------------------------------------------------------------

                            // repassa valores para pdf
                            $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));
                            if ($boVenc1 == true) {
                                $this->obRCarnePetropolis->setValor1      (number_format(round($nuValor1,2),2,',','.')) ;
                                if ($boVenc2 == true) {
                                    $this->obRCarnePetropolis->setValor2      (number_format(round($nuValor2,2),2,',','.')) ;
                                    if ($boVenc3 == true) {
                                        $this->obRCarnePetropolis->setValor3      (number_format(round($nuValor3,2),2,',','.')) ;
                                    }
                                }
                            }
                        } else {
                            $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));

                        }

                    }
                }

                $this->obRCarnePetropolis->setValorTotal( number_format(round($nuValorTotal,2),2,',','.') );
                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

                $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 100;

                if ($inCount == 1) {
                    $this->obRCarnePetropolis->novaPagina();
                    $inCount = 0;
                    $this->inVertical = 7;
                    $this->boPulaPagina = false;
                } else {
                    $this->boPulaPagina = true;
                    $inCount++;
                }

            }// fim foreach parcelas
        }

        //if ( ( $inSaltaPagina != count($arEmissao) ) && ( ( count($chave) != 2 ) && ( count($chave) != 3 ) ) ) {
        if (( $this->boPulaPagina ) && ( $inSaltaPagina != count($this->arEmissao) )) {
            $this->obRCarnePetropolis->novaPagina();
        }
        $arGruposValidos = explode(',','101,102,10120, 10121, 10122, 10123, 10124, 10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299');
        if( in_array($this->obRCarnePetropolis->getCodDivida(),$arGruposValidos))
            $this->obRCarnePetropolis->drawComplemento($this->inHorizontal, $this->inVertical);

    } // fim foreach $arEmissao

    if ( Sessao::read( 'stNomPdf' ) )
        $stNome     = Sessao::read( 'stNomPdf' );
    else
        $stNome     = "Carne.pdf";

    if ( Sessao::read( 'stParamPdf' ) )
        $stParam    = Sessao::read( 'stParamPdf' );
    else
        $stParam    = "I";
    $this->obRCarnePetropolis->show($stNome,$stParam); // lanca o pdf
}

function geraParcelas($data, $iteracao)
{
    $arDataResult = array();

    for ($i=0;$i<$iteracao;$i++) {
        $arData = explode('/',$data);

        $mes = $arData[1];
        $dia = $arData[0];
        $ano = $arData[2];

        switch ( (int) $mes ) {
            case 2 :
                    if ($ano % 4 == 0) {
                        $dia = 29;
                    } else {
                        $dia = 28;
                    }
            break;
            case 1 :
            case 3 :
            case 5 :
            case 7 :
            case 8 :
            case 10: $dia = 31;
            break;

            case 4 :
            case 6 :
            case 9 :
            case 11: $dia = 30;
            break;
        }

        $data = str_pad($dia,2,'0',STR_PAD_LEFT).'/'.str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$ano;
        array_push($arDataResult,$data);

        $mes++;
        if ($mes > 12) {
            $mes = 1;
            $ano++;
        }

        $data = $dia.'/'.$mes.'/'.$ano;
    }

    return $arDataResult;
}

}
