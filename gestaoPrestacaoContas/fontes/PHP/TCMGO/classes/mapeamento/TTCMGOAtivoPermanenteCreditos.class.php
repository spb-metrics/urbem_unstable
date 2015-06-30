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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAtivoPermanenteCreditos.class.php 62845 2015-06-29 13:16:59Z jean $

    * Casos de uso: uc-06.04.00
*/

include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php" );

class TTCMGOAtivoPermanenteCreditos  extends TContabilidadeBalancoFinanceiro
{
    public function TTCMGOAtivoPermanenteCreditos()
    {
        parent::TContabilidadeBalancoFinanceiro();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    function recuperaRegistro10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRegistro10().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

    public function montaRecuperaRegistro10()
    {
        $stDataIni = '01/01/'.$this->getDado( 'exercicio' );
        $stDataFim = '31/12/'.$this->getDado( 'exercicio' );
        $stSql = "
                    SELECT
                        0 AS numero_registro
                       , 10 AS tipo_registro
                       , LPAD('0,00'::VARCHAR,13,'0')::VARCHAR AS vl_cancelamento
                       , LPAD('0,00'::VARCHAR,13,'0')::VARCHAR AS vl_encampacao
                       ,*
                     FROM
                       tcmgo.ativo_permanente_creditos ( '" .$this->getDado( 'exercicio' ) .  "'::VARCHAR
                                                        , ' cod_estrutural ilike ''1.2%'' '::VARCHAR
                                                        ,'".$stDataIni."'::VARCHAR
                                                        ,'".$stDataFim."'::VARCHAR
                                                        ,'".$this->getDado ( 'stEntidades' )."'::VARCHAR
                                                       )
                         as retorno ( cod_estrutural varchar
                                     ,nivel             INTEGER
                                     ,nom_conta         VARCHAR
                                     ,num_orgao         INTEGER
                                     ,cod_unidade       INTEGER
                                     ,vl_saldo_anterior NUMERIC
                                     ,vl_saldo_debitos  NUMERIC
                                     ,vl_saldo_creditos NUMERIC
                                     ,vl_saldo_atual    NUMERIC
                                     ,nom_sistema       VARCHAR
                                     ,tipo_lancamento   INTEGER
                                    )
                    where vl_saldo_anterior <> 0
                       or vl_saldo_debitos <> 0
                       or vl_saldo_creditos <> 0
                    ORDER BY cod_estrutural ";
        return $stSql;
    }

}

?>
