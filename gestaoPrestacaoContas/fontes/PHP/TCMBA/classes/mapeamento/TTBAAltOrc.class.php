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
    * Data de Criação: 15/10/2007

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
Revision 1.1  2007/10/16 01:38:47  diego
Arquivos novos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 15/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAAltOrc extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAAltOrc()
{
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
    $stSql .= " SELECT   desp.exercicio           \n";
    $stSql .= "         ,desp.num_orgao          \n";
    $stSql .= "         ,desp.num_unidade          \n";
    $stSql .= "         ,desp.cod_funcao          \n";
    $stSql .= "         ,desp.cod_subfuncao          \n";
    $stSql .= "         ,desp.cod_programa          \n";
    $stSql .= "         ,replace(cont.cod_estrutural,'.','') as estrutural          \n";
    $stSql .= "         ,desp.num_pao          \n";
    $stSql .= "         ,orcamento.fn_consulta_tipo_pao(desp.exercicio,desp.num_pao) as tipo_pao          \n";
    $stSql .= "         ,desp.cod_recurso          \n";
    $stSql .= "         ,desp.vl_original          \n";
    $stSql .= "         ,su.cod_tipo          \n";
    $stSql .= "         ,su.cod_norma          \n";
    $stSql .= "         ,to_char(su.dt_suplementacao,'dd/mm/yyyy') as data_suplementacao          \n";
    $stSql .= "         ,coalesce(vl_suplementado,0.00)+coalesce(vl_reducao,0.00) as vl_suplementacao          \n";
    $stSql .= "         ,cod_tipo_tcm  as tipo_fundamento        \n";
    $stSql .= "         ,norm.num_norma          \n";
    $stSql .= "         ,norm.nom_norma          \n";
    $stSql .= "         ,to_char(norm.dt_publicacao,'dd/mm/yyyy') as data_publicacao          \n";
    $stSql .= "         ,CASE WHEN (su.cod_tipo = 15 )  THEN '1'          \n";
    $stSql .= "               WHEN (su.cod_tipo = 1  )  THEN '26'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 2  )  THEN '15'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 3  )  THEN '28'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 4  )  THEN '9'          \n";
    $stSql .= "               WHEN (su.cod_tipo = 5  )  THEN '12'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 6  )  THEN '25'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 7  )  THEN '13'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 8  )  THEN '28'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 9  )  THEN '7'          \n";
    $stSql .= "               WHEN (su.cod_tipo = 10 )  THEN '10'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 11 )  THEN '5'          \n";
    $stSql .= "               WHEN (su.cod_tipo = 12 )  THEN '30'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 13 )  THEN '29'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 14 )  THEN '31'         \n";
    $stSql .= "               WHEN (su.cod_tipo = 15 )  THEN '1'          \n";
    $stSql .= "               WHEN (su.cod_tipo = 16 )  THEN '1'          \n";
    $stSql .= "           END AS tipo_alteracao         \n";
    $stSql .= "           \n";
    $stSql .= " FROM    (          \n";
    $stSql .= "         SELECT exercicio          \n";
    $stSql .= "               ,cod_norma          \n";
    $stSql .= "               ,cod_tipo          \n";
    $stSql .= "               ,dt_suplementacao          \n";
    $stSql .= "               ,cod_despesa          \n";
    $stSql .= "               ,sum(vl_suplementado) as vl_suplementado          \n";
    $stSql .= "               ,sum(vl_reducao) as vl_reducao          \n";
    $stSql .= "         FROM (          \n";
    $stSql .= "             SELECT OS.exercicio          \n";
    $stSql .= "                   ,OS.cod_suplementacao          \n";
    $stSql .= "                   ,OS.cod_norma          \n";
    $stSql .= "                   ,OS.cod_tipo          \n";
    $stSql .= "                   ,OS.dt_suplementacao          \n";
    $stSql .= "                   ,OSS.cod_despesa          \n";
    $stSql .= "                   ,OSS.valor as vl_suplementado          \n";
    $stSql .= "                   ,0.00 as vl_reducao          \n";
    $stSql .= "             FROM orcamento.suplementacao AS OS          \n";
    $stSql .= "             LEFT JOIN orcamento.suplementacao_suplementada AS OSS          \n";
    $stSql .= "             ON( OSS.exercicio = OS.exercicio          \n";
    $stSql .= "             AND OSS.cod_suplementacao = OS.cod_suplementacao )          \n";
    $stSql .= "             AND OS.exercicio='".$this->getDado('exercicio')."'                    \n";
    $stSql .= "             UNION          \n";
    $stSql .= "             SELECT OS.exercicio          \n";
    $stSql .= "                   ,OS.cod_suplementacao          \n";
    $stSql .= "                   ,OS.cod_norma          \n";
    $stSql .= "                   ,OS.cod_tipo          \n";
    $stSql .= "                   ,OS.dt_suplementacao          \n";
    $stSql .= "                   ,OSR.cod_despesa          \n";
    $stSql .= "                   ,0.00 as vl_suplementado          \n";
    $stSql .= "                   ,OSR.valor as vl_reducao          \n";
    $stSql .= "             FROM orcamento.suplementacao AS OS          \n";
    $stSql .= "             LEFT JOIN orcamento.suplementacao_reducao AS OSR          \n";
    $stSql .= "             ON( OSR.exercicio = OS.exercicio          \n";
    $stSql .= "             AND OSR.cod_suplementacao = OS.cod_suplementacao )          \n";
    $stSql .= "             AND OS.exercicio='".$this->getDado('exercicio')."'                    \n";
    $stSql .= "             ) as tbl          \n";
    $stSql .= "         GROUP BY exercicio          \n";
    $stSql .= "               ,cod_despesa          \n";
    $stSql .= "               ,cod_norma          \n";
    $stSql .= "               ,cod_tipo          \n";
    $stSql .= "               ,dt_suplementacao          \n";
    $stSql .= "         ) as su          \n";
    $stSql .= "         ,orcamento.despesa          as desp          \n";
    $stSql .= "         ,orcamento.conta_despesa    as cont          \n";
    $stSql .= "         ,normas.norma               as norm          \n";
    $stSql .= "         LEFT JOIN tcmba.tipo_norma  as ttno          \n";
    $stSql .= "         ON (norm.cod_tipo_norma=ttno.cod_tipo)          \n";
    $stSql .= " WHERE   desp.exercicio  = cont.exercicio          \n";
    $stSql .= " AND     desp.cod_conta  = cont.cod_conta          \n";
    $stSql .= "           \n";
    $stSql .= " AND     desp.exercicio  = su.exercicio          \n";
    $stSql .= " AND     desp.cod_despesa=su.cod_despesa          \n";
    $stSql .= "           \n";
    $stSql .= " AND     su.cod_norma    = norm.cod_norma          \n";
    $stSql .= "           \n";
    $stSql .= " AND     desp.exercicio = 2007          \n";
    $stSql .= " AND     desp.exercicio='".$this->getDado('exercicio')."'                    \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "           AND   desp.cod_entidade in ( ".$this->getDado('stEntidades')." )   \n";
    }
    $stSql .= " ORDER BY  desp.exercicio          \n";
    $stSql .= "         ,desp.num_orgao          \n";
    $stSql .= "         ,desp.num_unidade          \n";
    $stSql .= "         ,desp.cod_funcao          \n";
    $stSql .= "         ,desp.cod_subfuncao          \n";
    $stSql .= "         ,desp.cod_programa          \n";
    $stSql .= "         ,replace(cont.cod_estrutural,'.','')          \n";
    $stSql .= "         ,desp.cod_recurso          \n";

    return $stSql;
}

}
