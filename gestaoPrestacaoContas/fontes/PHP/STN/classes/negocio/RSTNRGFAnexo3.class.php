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
    * Classe de Regra do Relatório do Anexo 3
    * Data de Criação   : 01/08/2006

    * @author Analista: Cleisson Barboz
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-06.01.22
*/

/*
$Log$
Revision 1.2  2006/08/04 13:48:27  jose.eduardo
Ajustes

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Extrato Bancario
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RSTNRGFAnexo3 extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inQuadrimestre;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade           = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setQuadrimestre($valor) { $this->inQuadrimestre           = $valor; }

/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                   }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;                      }
/*
    * @access Public
    * @return Integer
*/
function getQuadrimestre() { return $this->inQuadrimestre;                  }

/**
    * Método Construtor
    * @access Private
*/
function RSTNRGFAnexo3()
{
    $this->obRRelatorio                    = new RRelatorio;

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GPC_STN_MAPEAMENTO."FSTNRGFAnexo3.class.php");
    $obFSTNRGFAnexo3 = new FSTNRGFAnexo3;

    $obFSTNRGFAnexo3->setDado("stExercicio"          ,$this->getExercicio());
    $obFSTNRGFAnexo3->setDado("stEntidade"           ,$this->getEntidade());

    $obErro = $obFSTNRGFAnexo3->recuperaDadosRelatorioAnexo3( $rsAnexo3 );

    if ( !$obErro->ocorreu() ) {
        $obErro = $obFSTNRGFAnexo3->recuperaDadosReceitaLiquida ( $rsReceitaLiquida );
    }

    $arBloco1 = array();

    $arBloco1[0]["descricao" ]   = "RELATÓRIO DE GESTÃO FISCAL";
    $arBloco1[1]["descricao" ]   = "DEMONSTRATIVO DAS GARANTIAS E CONTRAGARANTIAS DE VALORES";
    $arBloco1[2]["descricao" ]   = "ORÇAMENTOS FISCAL E DA SEGURIDADE SOCIAL";

    $arBloco2 = array();

    $inCount = 0;

    $subTotalSaldoExercicioAnterior    = 0;
    $subTotalSaldoPrimeiroQuadrimestre = 0;
    $subTotalSaldoSegundoQuadrimestre  = 0;
    $subTotalSaldoTerceiroQuadrimestre = 0;

    while ( $rsAnexo3->getCampo("tipo") == "G" ) {

        if ( ($rsAnexo3->getCampo("descricao" ) == "EXTERNAS" ) || ($rsAnexo3->getCampo("descricao" ) == "INTERNAS" ) ) {
            $arBloco2[$inCount]["descricao"                  ]   = $rsAnexo3->getCampo("descricao"                  );
            $arBloco2[$inCount]["saldo_exercicio_anterior"   ]   = $rsAnexo3->getCampo("saldo_exercicio_anterior"   );
            $arBloco2[$inCount]["saldo_primeiro_quadrimestre"]   = $rsAnexo3->getCampo("saldo_primeiro_quadrimestre");
            $arBloco2[$inCount]["saldo_segundo_quadrimestre" ]   = $rsAnexo3->getCampo("saldo_segundo_quadrimestre" );
            $arBloco2[$inCount]["saldo_terceiro_quadrimestre"]   = $rsAnexo3->getCampo("saldo_terceiro_quadrimestre");
        } else {
            $arBloco2[$inCount]["descricao"                  ]   = str_pad($rsAnexo3->getCampo("descricao"),strlen($rsAnexo3->getCampo("descricao"))+4, " ", STR_PAD_LEFT);
            $arBloco2[$inCount]["saldo_exercicio_anterior"   ]   = number_format($rsAnexo3->getCampo("saldo_exercicio_anterior"   ), 2 ,"," , ".");
            $arBloco2[$inCount]["saldo_primeiro_quadrimestre"]   = number_format($rsAnexo3->getCampo("saldo_primeiro_quadrimestre"), 2 ,"," , ".");

            $subTotalSaldoExercicioAnterior    += $rsAnexo3->getCampo("saldo_exercicio_anterior"   );
            $subTotalSaldoPrimeiroQuadrimestre += $rsAnexo3->getCampo("saldo_primeiro_quadrimestre");

            if ( $this->getQuadrimestre() == 1 ) {
                $arBloco2[$inCount]["saldo_segundo_quadrimestre" ]   = number_format(0, 2 ,"," , ".");
                $arBloco2[$inCount]["saldo_terceiro_quadrimestre"]   = number_format(0, 2 ,"," , ".");

                $subTotalSaldoSegundoQuadrimestre  += 0;
                $subTotalSaldoTerceiroQuadrimestre += 0;
            } elseif ( $this->getQuadrimestre() == 2 ) {
                $arBloco2[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($rsAnexo3->getCampo("saldo_segundo_quadrimestre" ), 2 ,"," , ".");
                $arBloco2[$inCount]["saldo_terceiro_quadrimestre"]   = number_format(0, 2 ,"," , ".");

                $subTotalSaldoSegundoQuadrimestre  += $rsAnexo3->getCampo("saldo_segundo_quadrimestre" );
                $subTotalSaldoTerceiroQuadrimestre += 0;
            } elseif ( $this->getQuadrimestre() == 3 ) {
                $arBloco2[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($rsAnexo3->getCampo("saldo_segundo_quadrimestre" ), 2 ,"," , ".");
                $arBloco2[$inCount]["saldo_terceiro_quadrimestre"]   = number_format($rsAnexo3->getCampo("saldo_terceiro_quadrimestre"), 2 ,"," , ".");

                $subTotalSaldoSegundoQuadrimestre  += $rsAnexo3->getCampo("saldo_segundo_quadrimestre" );
                $subTotalSaldoTerceiroQuadrimestre += $rsAnexo3->getCampo("saldo_terceiro_quadrimestre");
            }

        }

        $inCount++;

        $rsAnexo3->proximo();

    }

    $arBloco3 = array();

    $inCount = 0;

    $arBloco3[$inCount]["descricao"                  ]   = "TOTAL";
    $arBloco3[$inCount]["saldo_exercicio_anterior"   ]   = number_format($subTotalSaldoExercicioAnterior   , 2, ",", ".");
    $arBloco3[$inCount]["saldo_primeiro_quadrimestre"]   = number_format($subTotalSaldoPrimeiroQuadrimestre, 2, ",", ".");
    $arBloco3[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($subTotalSaldoSegundoQuadrimestre , 2, ",", ".");
    $arBloco3[$inCount]["saldo_terceiro_quadrimestre"]   = number_format($subTotalSaldoTerceiroQuadrimestre, 2, ",", ".");

    $inCount++;

    $arBloco3[$inCount]["descricao"                  ]   = $rsReceitaLiquida->getCampo("descricao"                    );
    $arBloco3[$inCount]["saldo_exercicio_anterior"   ]   = number_format($rsReceitaLiquida->getCampo("receita_exercicio_anterior"   ), 2, ",", ".");
    $arBloco3[$inCount]["saldo_primeiro_quadrimestre"]   = number_format($rsReceitaLiquida->getCampo("receita_primeiro_quadrimestre"), 2, ",", ".");

    if ( $this->getQuadrimestre() == 1 ) {
        $arBloco3[$inCount]["saldo_segundo_quadrimestre" ]   = number_format(0, 2, ",", ".");
        $arBloco3[$inCount]["saldo_terceiro_quadrimestre"]   = number_format(0, 2, ",", ".");
    } elseif ( $this->getQuadrimestre() == 2 ) {
        $arBloco3[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($rsReceitaLiquida->getCampo("receita_segundo_quadrimestre" ), 2, ",", ".");
        $arBloco3[$inCount]["saldo_terceiro_quadrimestre"]   = number_format(0, 2, ",", ".");
    } elseif ( $this->getQuadrimestre() == 3 ) {
        $arBloco3[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($rsReceitaLiquida->getCampo("receita_segundo_quadrimestre" ), 2, ",", ".");
        $arBloco3[$inCount]["saldo_terceiro_quadrimestre"]   = number_format($rsReceitaLiquida->getCampo("receita_terceiro_quadrimestre"), 2, ",", ".");
    }

    $inCount++;

    $arBloco3[$inCount]["descricao"                  ]   = "% do TOTAL DAS GARANTIAS sobre a RCL ";

    if ($subTotalSaldoExercicioAnterior > 0) {
        $arBloco3[$inCount]["saldo_exercicio_anterior"   ]   = round(( $subTotalSaldoExercicioAnterior * 100 ) / $rsReceitaLiquida->getCampo("receita_exercicio_anterior") , 2)."%";
    } else {
        $arBloco3[$inCount]["saldo_exercicio_anterior"   ]   = "0%";
    }

    if ($subTotalSaldoPrimeiroQuadrimestre > 0) {
        $arBloco3[$inCount]["saldo_primeiro_quadrimestre"]   = round(( $subTotalSaldoPrimeiroQuadrimestre * 100 ) / $rsReceitaLiquida->getCampo("receita_primeiro_quadrimestre") , 2) ."%";
    } else {
        $arBloco3[$inCount]["saldo_primeiro_quadrimestre"]   = "0%";
    }

    if ($subTotalSaldoSegundoQuadrimestre > 0) {
        $arBloco3[$inCount]["saldo_segundo_quadrimestre" ]   = round(( $subTotalSaldoSegundoQuadrimestre * 100 ) / $rsReceitaLiquida->getCampo("receita_segundo_quadrimestre" ) , 2) ."%";
    } else {
        $arBloco3[$inCount]["saldo_segundo_quadrimestre" ]   = "0%";
    }

    if ($subTotalSaldoTerceiroQuadrimestre > 0) {
        $arBloco3[$inCount]["saldo_terceiro_quadrimestre"]   = round(( $subTotalSaldoTerceiroQuadrimestre * 100 ) / $rsReceitaLiquida->getCampo("receita_terceiro_quadrimestre") , 2)."%";
    } else {
        $arBloco3[$inCount]["saldo_terceiro_quadrimestre"]   = "0%";
    }

    $arBloco4 = array();

    $arBloco4[0]["descricao"                  ]   = "LIMITE DEFINIDO POR RESOLUÇÃO DO";
    $arBloco4[0]["saldo_exercicio_anterior"   ]   = "";

    $arBloco5 = array();

    $arBloco5[0]["descricao"                  ]   = "SENADO FEDERAL 32%";
    $arBloco5[0]["saldo_exercicio_anterior"   ]   = "";

    $arBloco6 = array();

    $inCount = 0;

    $subTotalSaldoExercicioAnterior    = 0;
    $subTotalSaldoPrimeiroQuadrimestre = 0;
    $subTotalSaldoSegundoQuadrimestre  = 0;
    $subTotalSaldoTerceiroQuadrimestre = 0;

    while ( $rsAnexo3->getCampo("tipo") == "CG" ) {

        if ( ($rsAnexo3->getCampo("descricao" ) == "GARANTIAS EXTERNAS" ) || ($rsAnexo3->getCampo("descricao" ) == "GARANTIAS INTERNAS" ) ) {
            $arBloco6[$inCount]["descricao"                  ]   = $rsAnexo3->getCampo("descricao"                  );
            $arBloco6[$inCount]["saldo_exercicio_anterior"   ]   = $rsAnexo3->getCampo("saldo_exercicio_anterior"   );
            $arBloco6[$inCount]["saldo_primeiro_quadrimestre"]   = $rsAnexo3->getCampo("saldo_primeiro_quadrimestre");
            $arBloco6[$inCount]["saldo_segundo_quadrimestre" ]   = $rsAnexo3->getCampo("saldo_segundo_quadrimestre" );
            $arBloco6[$inCount]["saldo_terceiro_quadrimestre"]   = $rsAnexo3->getCampo("saldo_terceiro_quadrimestre");
        } else {
            $arBloco6[$inCount]["descricao"                  ]   = str_pad($rsAnexo3->getCampo("descricao"), strlen($rsAnexo3->getCampo("descricao"))+4, " ", STR_PAD_LEFT);
            $arBloco6[$inCount]["saldo_exercicio_anterior"   ]   = number_format($rsAnexo3->getCampo("saldo_exercicio_anterior"   ), 2, ",", ".");
            $arBloco6[$inCount]["saldo_primeiro_quadrimestre"]   = number_format($rsAnexo3->getCampo("saldo_primeiro_quadrimestre"), 2, ",", ".");

            $subTotalSaldoExercicioAnterior    += $rsAnexo3->getCampo("saldo_exercicio_anterior"   );
            $subTotalSaldoPrimeiroQuadrimestre += $rsAnexo3->getCampo("saldo_primeiro_quadrimestre");

            if ( $this->getQuadrimestre() == 1 ) {
                $arBloco6[$inCount]["saldo_segundo_quadrimestre" ]   = number_format(0, 2, ",", ".");
                $arBloco6[$inCount]["saldo_terceiro_quadrimestre"]   = number_format(0, 2, ",", ".");

                $subTotalSaldoSegundoQuadrimestre  += 0;
                $subTotalSaldoTerceiroQuadrimestre += 0;
            } elseif ( $this->getQuadrimestre() == 2 ) {
                $arBloco6[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($rsAnexo3->getCampo("saldo_segundo_quadrimestre" ), 2, ",", ".");
                $arBloco6[$inCount]["saldo_terceiro_quadrimestre"]   = number_format(0, 2, ",", ".");

                $subTotalSaldoSegundoQuadrimestre  += 0;
                $subTotalSaldoTerceiroQuadrimestre += 0;
            } elseif ( $this->getQuadrimestre() == 3 ) {
                $arBloco6[$inCount]["saldo_segundo_quadrimestre" ]   = number_format($rsAnexo3->getCampo("saldo_segundo_quadrimestre" ), 2, ",", ".");
                $arBloco6[$inCount]["saldo_terceiro_quadrimestre"]   = number_format($rsAnexo3->getCampo("saldo_terceiro_quadrimestre"), 2, ",", ".");

                $subTotalSaldoSegundoQuadrimestre  += $rsAnexo3->getCampo("saldo_segundo_quadrimestre" );
                $subTotalSaldoTerceiroQuadrimestre += $rsAnexo3->getCampo("saldo_terceiro_quadrimestre");
            }
        }

        $inCount++;

        $rsAnexo3->proximo();
    }

    $arBloco7 = array();

    $arBloco7[0]["descricao"                  ]   = "TOTAL CONTRAGARANTIAS";
    $arBloco7[0]["saldo_exercicio_anterior"   ]   = number_format($subTotalSaldoExercicioAnterior   , 2, ",", ".");
    $arBloco7[0]["saldo_primeiro_quadrimestre"]   = number_format($subTotalSaldoPrimeiroQuadrimestre, 2, ",", ".");
    $arBloco7[0]["saldo_segundo_quadrimestre" ]   = number_format($subTotalSaldoSegundoQuadrimestre , 2, ",", ".");
    $arBloco7[0]["saldo_terceiro_quadrimestre"]   = number_format($subTotalSaldoTerceiroQuadrimestre, 2, ",", ".");

    $arBloco8 = array();

    $arBloco8[0]["descricao"                  ]   = "FONTE:";

    $rsBloco1  = new RecordSet;
    $rsBloco1->preenche($arBloco1);

    $rsBloco2  = new RecordSet;
    $rsBloco2->preenche($arBloco2);

    $rsBloco3  = new RecordSet;
    $rsBloco3->preenche($arBloco3);

    $rsBloco4  = new RecordSet;
    $rsBloco4->preenche($arBloco4);

    $rsBloco5  = new RecordSet;
    $rsBloco5->preenche($arBloco5);

    $rsBloco6  = new RecordSet;
    $rsBloco6->preenche($arBloco6);

    $rsBloco7  = new RecordSet;
    $rsBloco7->preenche($arBloco7);

    $rsBloco8  = new RecordSet;
    $rsBloco8->preenche($arBloco8);

    $rsRecordSet = array($rsBloco1, $rsBloco2, $rsBloco3, $rsBloco4, $rsBloco5, $rsBloco6, $rsBloco7, $rsBloco8);

    return $obErro;

}

}
