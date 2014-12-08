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
    * Classe de regra de negócio UniOrcam
    * Data de Criação: 10/02/2005

    * @author Analista: Diego B. ictoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.05
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_MAPEAMENTO."TExportacaoMANADUniOrcam.class.php"     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"  	);
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"  	);

class RExportacaoMANADUniOrcam extends ROrcamentoUnidadeOrcamentaria
{
var $obRCGMPessoaJuridica;
var $obTExportacaoMANADUniOrcam;
var $inIdentificador;

//SETTERS
function setRCGMPessoaJuridica($valor) { $this->obRCGMPessoaJuridica = $valor;  }
function setTExportacaoMANADUniOrcam($valor) { $this->obTExportacaoMANADUniOrcam     = $valor;  }
function setIdentificador($valor) { $this->inIdentificador      = $valor;  }

//GETTERS
function getRCGMPessoaJuridica() { return $this->obRCGMPessoaJuridica; }
function getTExportacaoMANADUniOrcam() { return $this->obTExportacaoMANADUniOrcam;     }
function getIdentificador() { return $this->inIdentificador;      }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoMANADUniOrcam()
{
    parent::ROrcamentoUnidadeOrcamentaria();
    $this->setRCGMPessoaJuridica( new RCGMPessoaJuridica() );
    $this->setTExportacaoMANADUniOrcam( new TExportacaoMANADUniOrcam() );
}

function salvar($obTransacao = "")
{
    $obErro = new Erro();
    if ( $this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "" ) {
        $this->obTExportacaoMANADUniOrcam->setDado( "num_orgao", $this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        $this->obTExportacaoMANADUniOrcam->setDado( "num_unidade", $this->getNumeroUnidade() );
        $this->obTExportacaoMANADUniOrcam->setDado( "identificador", $this->getIdentificador() );
        $this->obTExportacaoMANADUniOrcam->setDado( "numcgm", $this->obRCGMPessoaJuridica->getNumCGM() );
        $this->obTExportacaoMANADUniOrcam->setDado( "exercicio", $this->getExercicio() );
        $this->obTExportacaoMANADUniOrcam->recuperaPorChave($rsUniOrcam, $boTransacao);
        if ( $rsUniOrcam->eof() ) {
            $obErro = $this->obTExportacaoMANADUniOrcam->inclusao( $boTransacao );
        } else {
            $obErro = $this->obTExportacaoMANADUniOrcam->alteracao( $boTransacao );
        }
    } elseif ( ($this->getIdentificador() != "" AND  $this->obRCGMPessoaJuridica->getNumCGM() == "") OR ($this->getIdentificador() == "" AND  $this->obRCGMPessoaJuridica->getNumCGM() != "") ) {
        $obErro->setDescricao("Para o orgão (".$this->obROrcamentoOrgaoOrcamentario->getNumeroOrgao().") e unidade (".$this->getNumeroUnidade()."), não foi informado o identificador ou cgm");
    }

    return $obErro;
}

function listar(&$rsUnidadeOrcamento, $boTransacao = "")
{
    $this->obTExportacaoMANADUniOrcam->setDado( 'exercicio',$this->getExercicio() );
    $stOrder = "num_orgao,num_unidade";
    $obErro = $this->obTExportacaoMANADUniOrcam->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarDadosConversao(&$rsUnidadeOrcamento, $boTransacao = "")
{
    $stOrder = "exercicio,num_orgao,num_unidade";
    $this->obTExportacaoMANADUniOrcam->setDado( 'exercicio',$this->getExercicio() );
    $obErro = $this->obTExportacaoMANADUniOrcam->recuperaDadosUniOrcamConversao( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
