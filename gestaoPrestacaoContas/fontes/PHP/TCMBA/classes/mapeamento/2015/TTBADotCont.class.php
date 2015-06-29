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
    * Data de Criação: 04/09/2007

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
Revision 1.3  2007/10/13 20:05:49  diego
Corrigindo formatação e informações

Revision 1.2  2007/09/25 03:38:19  diego
Comitando correção no filtro

Revision 1.1  2007/09/06 00:42:15  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 05/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBADotCont extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBADotacao()
{
    parent::TOrcamentoDespesa();

    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
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
    $stSql .= " SELECT   soid.exercicio          \n";
    $stSql .= "         ,contr.num_contrato        \n";
    $stSql .= "         ,soid.cod_despesa        \n";
    $stSql .= "         \n";
    $stSql .= "         ,desp.exercicio        \n";
    $stSql .= "         ,desp.num_orgao        \n";
    $stSql .= "         ,desp.num_unidade        \n";
    $stSql .= "         ,desp.cod_funcao        \n";
    $stSql .= "         ,desp.cod_subfuncao          \n";
    $stSql .= "         ,desp.cod_programa          \n";
    $stSql .= "         ,desp.num_pao          \n";
    $stSql .= "         ,replace(cont.cod_estrutural,'.','') as estrutural          \n";
    $stSql .= "         ,orcamento.fn_consulta_tipo_pao(desp.exercicio,desp.num_pao) as tipo_pao          \n";
    $stSql .= "         ,desp.cod_recurso          \n";
    $stSql .= "         \n";
    $stSql .= " FROM     licitacao.licitacao                as lici          \n";
    $stSql .= "         ,compras.mapa                       as mapa          \n";
    $stSql .= "         ,compras.mapa_solicitacao           as maso          \n";
    $stSql .= "         ,compras.mapa_item                  as mait          \n";
    $stSql .= "         ,compras.solicitacao_item           as soit          \n";
    $stSql .= "         ,compras.solicitacao_item_dotacao   as soid          \n";
    $stSql .= "         \n";
    $stSql .= "         ,licitacao.contrato                 as contr          \n";
    $stSql .= "         \n";
    $stSql .= "         ,orcamento.despesa          as desp          \n";
    $stSql .= "         ,orcamento.conta_despesa    as cont          \n";
    $stSql .= " WHERE   lici.exercicio              = mapa.exercicio          \n";
    $stSql .= " AND     lici.cod_mapa               = mapa.cod_mapa          \n";
    $stSql .= " AND     mapa.exercicio              = maso.exercicio          \n";
    $stSql .= " AND     mapa.cod_mapa               = maso.cod_mapa          \n";
    $stSql .= " AND     maso.exercicio              = mait.exercicio          \n";
    $stSql .= " AND     maso.cod_entidade           = mait.cod_entidade          \n";
    $stSql .= " AND     maso.cod_solicitacao        = mait.cod_solicitacao          \n";
    $stSql .= " AND     maso.cod_mapa               = mait.cod_mapa          \n";
    $stSql .= " AND     maso.exercicio_solicitacao  = mait.exercicio_solicitacao          \n";
    $stSql .= " AND     mait.exercicio              = soit.exercicio          \n";
    $stSql .= " AND     mait.cod_entidade           = soit.cod_entidade          \n";
    $stSql .= " AND     mait.cod_solicitacao        = soit.cod_solicitacao          \n";
    $stSql .= " AND     mait.cod_centro             = soit.cod_centro          \n";
    $stSql .= " AND     mait.cod_item               = soit.cod_item          \n";
    $stSql .= " AND     soit.exercicio              = soid.exercicio          \n";
    $stSql .= " AND     soit.cod_entidade           = soid.cod_entidade          \n";
    $stSql .= " AND     soit.cod_solicitacao        = soid.cod_solicitacao          \n";
    $stSql .= " AND     soit.cod_centro             = soid.cod_centro          \n";
    $stSql .= " AND     soit.cod_item               = soid.cod_item          \n";
    $stSql .= "           \n";
    $stSql .= " AND     lici.exercicio              = contr.exercicio        \n";
    $stSql .= " AND     lici.cod_entidade           = contr.cod_entidade        \n";
    $stSql .= " AND     lici.cod_modalidade         = contr.cod_modalidade        \n";
    $stSql .= " AND     lici.cod_licitacao          = contr.cod_licitacao        \n";
    $stSql .= "           \n";
    $stSql .= " AND     soid.exercicio              = desp.exercicio          \n";
    $stSql .= " AND     soid.cod_despesa            = desp.cod_despesa          \n";
    $stSql .= " AND     soid.exercicio              = cont.exercicio          \n";
    $stSql .= " AND     soid.cod_conta              = cont.cod_conta          \n";
    //$stSql .= " AND     lici.exercicio='".$this->getDado('exercicio')."'                    \n";
    //if ( $this->getDado('stEntidades') ) {
    //    $stSql .= "           AND   lici.cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    //}
    return $stSql;
}

}
