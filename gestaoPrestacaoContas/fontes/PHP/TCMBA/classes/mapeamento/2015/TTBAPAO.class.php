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
    * Data de Criação: 03/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/07/04 02:46:21  diego
Primeira versão.

Revision 1.1  2007/06/22 22:50:29  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php" );

/**
  *
  * Data de Criação: 13/06/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPAO extends TOrcamentoProjetoAtividade
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPAO()
{
    parent::TOrcamentoProjetoAtividade();

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
    $stSql .= " SELECT   pao.exercicio                                                      \n";
    $stSql .= "         ,des.cod_entidade                                                   \n";
    $stSql .= "         ,pao.num_pao                                                        \n";
    $stSql .= "         ,pao.nom_pao                                                        \n";
    $stSql .= "         ,des.cod_funcao                                                     \n";
    $stSql .= "         ,des.cod_subfuncao                                                  \n";
    $stSql .= "         ,des.cod_programa                                                   \n";
    $stSql .= "         ,orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao) as tipo  \n";
    $stSql .= "         ,substr(detalhamento,1,120) as detalha                              \n";
    $stSql .= " FROM     orcamento.pao as pao                                               \n";
    $stSql .= "         ,orcamento.despesa as des                                           \n";
    $stSql .= " WHERE   pao.exercicio   = des.exercicio                                     \n";
    $stSql .= " AND     pao.num_pao     = des.num_pao                                       \n";
    $stSql .= " AND     pao.exercicio='".$this->getDado('exercicio')."'                     \n";
    $stSql .= " GROUP BY pao.exercicio                                                      \n";
    $stSql .= "         ,des.cod_entidade                                                   \n";
    $stSql .= "         ,pao.num_pao                                                        \n";
    $stSql .= "         ,pao.nom_pao                                                        \n";
    $stSql .= "         ,des.cod_funcao                                                     \n";
    $stSql .= "         ,des.cod_subfuncao                                                  \n";
    $stSql .= "         ,des.cod_programa                                                   \n";
    $stSql .= "         ,substr(detalhamento,1,120)                                         \n";

    return $stSql;
}

}
