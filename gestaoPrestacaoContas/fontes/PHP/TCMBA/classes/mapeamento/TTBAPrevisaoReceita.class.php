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
    * Extensão da Classe de mapeamento
    * Data de Criação: 09/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/07/11 04:46:53  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );

/**
  *
  * Data de Criação: 09/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPrevisaoReceita extends TOrcamentoPrevisaoReceita
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPrevisaoReceita()
{
    parent::TOrcamentoPrevisaoReceita();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT   cr.exercicio                                       \n";
    $stSql .= "         ,replace(cr.cod_estrutural,'.','') as estrutural    \n";
    $stSql .= "         ,sum(vl_periodo) as valor                           \n";
    $stSql .= " FROM     orcamento.conta_receita    as cr                   \n";
    $stSql .= "         ,orcamento.receita          as re                   \n";
    $stSql .= "         ,orcamento.previsao_receita as pr                   \n";
    $stSql .= " WHERE   cr.exercicio    = re.exercicio                      \n";
    $stSql .= " AND     cr.cod_conta    = re.cod_conta                      \n";
    $stSql .= " AND     re.exercicio    = pr.exercicio                      \n";
    $stSql .= " AND     re.cod_receita  = pr.cod_receita                    \n";
    $stSql .= " AND     cr.exercicio='".$this->getDado('exercicio')."'      \n";
    $stSql .= " GROUP BY cr.exercicio, cr.cod_estrutural                    \n";
    $stSql .= " ORDER BY cr.exercicio, cr.cod_estrutural                    \n";

    return $stSql;
}

}
