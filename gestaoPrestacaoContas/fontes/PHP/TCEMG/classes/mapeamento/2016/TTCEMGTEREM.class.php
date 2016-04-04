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
  * Arquivo de mapeamento - Exportação Arquivos TCEMG - TEREM.csv
  * Data de Criação: 14/03/2016

  * @author Analista:      Dagiane
  * @author Desenvolvedor: Jean
  *
  * @ignore
  * $Id: TTCEMGTEREM.class.php 64792 2016-04-01 13:51:20Z michel $
  * $Date: 2016-04-01 10:51:20 -0300 (Sex, 01 Abr 2016) $
  * $Author: michel $
  * $Rev: 64792 $
  *
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEMGTEREM extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stFiltro = "" , $stOrder = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = "
          SELECT 10 AS tiporegistro
               , teto_remuneratorio.teto AS vlparateto
               , CASE WHEN teto_remuneratorio_controle.cod_entidade IS NOT NULL
                      THEN 2
                      ELSE 1
                  END AS tipocadastro
               , teto_remuneratorio.justificativa AS justalteracao
            FROM tcemg.teto_remuneratorio
       LEFT JOIN tcemg.teto_remuneratorio_controle
              ON teto_remuneratorio_controle.cod_entidade = teto_remuneratorio.cod_entidade
             AND teto_remuneratorio_controle.exercicio = teto_remuneratorio.exercicio
           WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."','dd/mm/yyyy'))
             AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                   FROM tcemg.teto_remuneratorio
                                                  WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."','dd/mm/yyyy'))
                                               )
        ";
        return $stSql;
    }

    public function __destruct(){}
}
?>