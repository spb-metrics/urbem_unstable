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
    * Classe de mapeamento da tabela ORCAMENTO.SOMATORIO_RECEITA_ORCADA_ARRECADADA
    * Data de Criação: 05/10/2004

    * @author Analista: Jorge Ribarr

    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.07
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoSomatorioReceitaOrcadaArrecadada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoSomatorioReceitaOrcadaArrecadada()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_somatorio_receita_orcada_arrecadada');

    $this->AddCampo('receita'                   ,'integer',false,''    ,false,false);
    $this->AddCampo('descricao'                 ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_orcado'                 ,'numeric',false,'14.2',false,false);
    $this->AddCampo('arrecadado'                ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select *                                                                  \n";
    $stSql .= "from                                                                      \n";
    $stSql .= "              ".$this->getTabela()."(                                     \n";
    $stSql .= "              '".$this->getDado("exercicio")."',                          \n";
    $stSql .= "              '',                                                         \n";
    $stSql .= "              '".$this->getDado("data_inicial")."',                       \n";
    $stSql .= "              '".$this->getDado("data_final")."',                         \n";
    $stSql .= "               ".$this->getDado("stFiltro").")                            \n";
    $stSql .= "           as retorno(                                                    \n";
    $stSql .= "    receita                 varchar,                                      \n";
    $stSql .= "    descricao               varchar,                                      \n";
    $stSql .= "    vl_orcado               numeric,                                      \n";
    $stSql .= "    vl_arrecadado           numeric,                                       \n";
    $stSql .= "    nivel                    integer                                       \n";
    $stSql .= ")                                                                           ";

    return $stSql;
}

}
