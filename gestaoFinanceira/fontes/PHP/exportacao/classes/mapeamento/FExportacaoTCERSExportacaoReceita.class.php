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
    * Classe de mapeamento da tabela FN_ExportacaoTCERS_EXPORTACAO_RECEITA
    * Data de Criação: 04/03/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.01
                    uc-02.08.07
*/

/*
$Log$
Revision 1.10  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoTCERSExportacaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoTCERSExportacaoReceita()
{
    parent::Persistente();
    $this->setTabela('tcers.fn_exportacao_receita');
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "select\n";
    $stSql .= "*\n";
    $stSql .= "from\n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("stExercicio")     ."',    \n";
    $stSql .= "                        '".$this->getDado("stCodEntidades")  ."',    \n";
    $stSql .= "                         ".$this->getDado("inBimestre")       .")    \n";
    $stSql .= "AS tabela              ( cod_estrutural varchar,                     \n";
    $stSql .= "                         receita_mes1 numeric,                       \n";
    $stSql .= "                         receita_mes2 numeric,                       \n";
    $stSql .= "                         receita_mes3 numeric,                       \n";
    $stSql .= "                         receita_mes4 numeric,                       \n";
    $stSql .= "                         receita_mes5 numeric,                       \n";
    $stSql .= "                         receita_mes6 numeric,                       \n";
    $stSql .= "                         receita_mes7 numeric,                       \n";
    $stSql .= "                         receita_mes8 numeric,                       \n";
    $stSql .= "                         receita_mes9 numeric,                       \n";
    $stSql .= "                         receita_mes10 numeric,                      \n";
    $stSql .= "                         receita_mes11 numeric,                      \n";
    $stSql .= "                         receita_mes12 numeric,                      \n";
    $stSql .= "                         meta_arrecadacao1 numeric,                  \n";
    $stSql .= "                         meta_arrecadacao2 numeric,                  \n";
    $stSql .= "                         meta_arrecadacao3 numeric,                  \n";
    $stSql .= "                         meta_arrecadacao4 numeric,                  \n";
    $stSql .= "                         meta_arrecadacao5 numeric,                  \n";
    $stSql .= "                         meta_arrecadacao6 numeric                   \n";
    $stSql .= ")                                                                    \n";

    return $stSql;
}

/**
    * Executa funcao fn_exportacao_receita no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
